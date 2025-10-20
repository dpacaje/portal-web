<?php

namespace App\Services\PermisoCirculacion;

use App\Enums\EstadoPagoEnum;
use App\Interfaces\PermisoCirculacion\WebPagoRepositoryInterface;
use App\Services\Transbank\TbkService;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;

class WebPagoService
{
    public function __construct(
        protected readonly WebPagoRepositoryInterface $_webPagoRepository,
        protected readonly MaestroPermisoService $_maestroPermisoService,
        protected readonly MultaTransitoService $_multaTransitoService,
        protected readonly TbkService $_tbkService,
    ) {}

    public function buscarPorId(string $pago_id)
    {
        return $this->_webPagoRepository->obtenerPorPagoId($pago_id);
    }

    public function validarUltimosIntentosPagos(string $placa)
    {
        $fecha_limite = calcular_fecha();
        $ultimo_intento_pago = $this->_webPagoRepository->obtenerUltimoIntentoPago($placa, $fecha_limite);

        if (!$ultimo_intento_pago) {
            return false;
        }

        $pago_id = $ultimo_intento_pago->pago_id;
        $token = substr($ultimo_intento_pago->token, -64, 64);

        return $this->_tbkService->validarTransaccionPorToken($pago_id, $token);
    }

    public function crear(int $tipoportal, string $placa, string $detalle_permiso, int $monto_permiso, string $detalle_multa, int $monto_multa)
    {
        return DB::transaction(function () use ($tipoportal, $placa, $detalle_permiso, $monto_permiso, $detalle_multa, $monto_multa) {
            $prefijo = $tipoportal . date('Y');
            $ultimo_registro = $this->_webPagoRepository->obtenerUltimoPagoId($prefijo);
            $ultimo_id = $ultimo_registro ? $ultimo_registro->pago_id : null;
            $nuevo_id = obtenerNuevoPagoId($tipoportal, $ultimo_id);
            $total = $monto_permiso + $monto_multa;

            $data = [
                'pago_id' => $nuevo_id,
                'fecha_pago' => 0,
                'estado' => 'Creado',
                'placa_gral' => $placa,
                'permiso_array' => $detalle_permiso,
                'monto_permiso' => $monto_permiso,
                'multa_array' => $detalle_multa,
                'monto_multa' => $monto_multa,
                'total_pago' => $total,
                'fecha_stamp' => date('YmdHis'),
                'TBK_ORDEN_COMPRA' => $nuevo_id,
                'sistema_pagador' => 'PORTAL'
            ];

            $resultado = $this->_webPagoRepository->crear($data);

            return $resultado ? $nuevo_id : false;
        });
    }

    public function iniciarTransaccion(string $pago_id, int $monto)
    {
        return $this->_tbkService->iniciarTransaccion($pago_id, $monto);
    }

    public function actualizarToken(string $pago_id, string $url_token)
    {
        $sistema_pagador = 'Desconocido';
        if (Agent::isMobile()) {
            $sistema_pagador = 'Movil';
        } else if (Agent::isTablet()) {
            $sistema_pagador = 'Tablet';
        } else if (Agent::isDesktop()) {
            $sistema_pagador = 'Escritorio';
        }

        $ecosistema = Agent::platform();
        $navegador = Agent::browser();
        $navegador_version = Agent::version($navegador);

        return $this->_webPagoRepository->agregarToken($pago_id, $url_token, $sistema_pagador, $ecosistema, $navegador, $navegador_version);
    }

    public function validarTransaccion(string $session_id)
    {
        $tbk_obj = $this->_tbkService->buscarPorSessionId($session_id);

        if ($tbk_obj->status != 'AUTHORIZED' || $tbk_obj->response_code != 0) {
            $this->rechazar($tbk_obj, $session_id);
            return $tbk_obj->buy_order;
        }

        $this->pagar($tbk_obj, $session_id);

        $datos_wp = $this->_webPagoRepository->obtenerPorPagoId($tbk_obj->buy_order);

        if (!$datos_wp) {
            throw new \Exception('Error al consultar por wp.');
        }

        $this->_maestroPermisoService->pagarPermisos($tbk_obj->buy_order, $datos_wp->permiso_array);

        $this->_multaTransitoService->pagarMultas($tbk_obj->buy_order, $datos_wp->multa_array);

        // enviar correo

        // centralizar pagos

        return $tbk_obj->buy_order;
    }

    public function pagar($tbk_obj, $session_id)
    {
        $pago_id = $tbk_obj->buy_order;
        $data = [
            'estado' => EstadoPagoEnum::PAGADO,
            'fecha_pago' => date('YmdHis'),
            'session_id' => $session_id,
            'TBK_ORDEN_COMPRA' => $tbk_obj->buy_order,
            'TBK_TIPO_TRANSACCION' => 'TR_NORMAL',
            'TBK_RESPUESTA' => $tbk_obj->response_code,
            'TBK_MONTO' => $tbk_obj->amount,
            'TBK_CODIGO_AUTORIZACION' => $tbk_obj->authorization_code,
            'TBK_FINAL_NUMERO_TARJETA' => $tbk_obj->card_detail->card_number,
            'TBK_FECHA_CONTABLE' => $tbk_obj->accounting_date,
            'TBK_FECHA_TRANSACCION' => $tbk_obj->transaction_date,
            'TBK_ID_SESION' => $tbk_obj->session_id,
            'TBK_TIPO_PAGO' => $tbk_obj->payment_type_code,
            'TBK_NUMERO_CUOTAS' => $tbk_obj->installments_number
        ];
        return $this->_webPagoRepository->pagar($pago_id, $data);
    }
    
    public function rechazar($tbk_obj, $session_id)
    {
        $pago_id = $tbk_obj->buy_order;
        $data = [
            'estado' => 'Rechazada',
            'session_id' => $session_id,
            'TBK_ORDEN_COMPRA' => $tbk_obj->buy_order,
            'TBK_RESPUESTA' => $tbk_obj->response_code,
            'TBK_FECHA_TRANSACCION' => $tbk_obj->transaction_date
        ];
        return $this->_webPagoRepository->rechazar($pago_id, $data);
    }
}