<?php

namespace App\Services\Transbank;

use App\Libraries\Tbk;

class TbkService
{
    public function __construct(
        protected readonly Tbk $_tbk,
    ) {}

    public function validarTransaccionPorToken(string $buy_order, string $token)
    {
        $data = $this->_tbk->estadoPortoken($token);

        $data_json = json_decode($data['response']);

        if (isset($data_json->status) && isset($data_json->response_code) && isset($data_json->buy_order)) {
            if ($data_json->status == 'AUTHORIZED' && $data_json->response_code == 0 && $data_json->buy_order == $buy_order) {
                return true;
            }
        }
        return false;
    }

    public function iniciarTransaccion(string $buy_order, int $monto)
    {
        $data = $this->_tbk->crear($buy_order, $monto);

        $data_json = json_decode($data['response']);

        if (!isset($data_json->success) || !isset($data_json->data)) {
            throw new \Exception('No se pudo generar transaccion tbk, no existe success o data.');
        }

        if (!$data_json->success || strlen($data_json->data) < 100) {
            throw new \Exception('No se pudo generar transaccion tbk, success es false o longitud data menor 100.');
        }

        return $data_json->data;
    }

    public function buscarPorSessionId(string $session_id)
    {
        $data = $this->_tbk->estadoPorSessionId($session_id);

        $data_json = json_decode($data['response']);

        if (!isset($data_json->comprobante)) {
            throw new \Exception('No se pudo obtener comprobante de transaccion tbk, de session_id=' . $session_id);
        }

        if (!isset($data_json->comprobante->status) || !isset($data_json->comprobante->response_code) || !isset($data_json->comprobante->buy_order)) {
            throw new \Exception('No se pudo obtener transaccion tbk, status|response_code|buy_order, de session_id=' . $session_id);
        }

        return $data_json->comprobante;
    }
}