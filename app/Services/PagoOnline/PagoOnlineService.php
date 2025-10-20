<?php

namespace App\Services\PagoOnline;

use App\Services\PermisoCirculacion\WebPagoService;

class PagoOnlineService
{
    public function __construct(
        protected readonly WebPagoService $_webPagoService,
    ) {}

    public function gestionarInicioPago(array $data)
    {
        $pago_id = false;

        if ($data['tipoportal']->value == 10) {
            $pago_id = $this->_webPagoService->crear($data['tipoportal']->value, $data['placa'], $data['detalle_permiso'], $data['monto_permiso'], $data['detalle_multa'], $data['monto_multa']);
        } else if ($data['tipoportal']->value == 40) {
            // $pago_id = $this->_webPagoService->crear($data['placa'], $data['detalle_permiso'], $data['monto_permiso'], $data['detalle_multa'], $data['monto_multa']);
        } else if ($data['tipoportal']->value == 60) {
            // $pago_id = $this->_webPagoService->crear($data['placa'], $data['detalle_permiso'], $data['monto_permiso'], $data['detalle_multa'], $data['monto_multa']);
        } else {
            throw new \Exception('Tipo de Portal desconocido.');
        }

        if ($pago_id === false) {
            throw new \Exception('No se pudo generar nuevo pago_id.');
        }

        try {
            // $tbk = $_tbkService->iniciarTransaccion($pago_id, $total);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}