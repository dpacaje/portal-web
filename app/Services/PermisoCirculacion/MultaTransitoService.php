<?php

namespace App\Services\PermisoCirculacion;

use App\Interfaces\ParametrosGenerales\ParametrosGeneralesRepositoryInterface;
use App\Interfaces\PermisoCirculacion\MultaTransitoRepositoryInterface;
use Illuminate\Support\Facades\Log;

class MultaTransitoService
{
    public function __construct(
        protected readonly MultaTransitoRepositoryInterface $_multaTransitoRepository,
        protected readonly ParametrosGeneralesRepositoryInterface $_parametrosGeneralesRepository,
    ) {}

    public function obtenerDeudaMonto(string $placa)
    {
        $utm = $this->_parametrosGeneralesRepository->getUtm(date('Y'), date('m'));

        if (is_null($utm)) {
            throw new \Exception('No se pudo obtener el utm actual');
        }

        $monto_multa = 0;

        $deuda = $this->_multaTransitoRepository->obtenerDeuda($placa);

        foreach ($deuda as $item) {
            $monto_multa += calcular_multa_transito($item->fecha, $item->nivel, $utm->utm_utm, $item->valor);
        }

        return $monto_multa;
    }

    public function obtenerDeudaArray(string $placa)
    {
        $deuda = $this->_multaTransitoRepository->obtenerDeuda($placa);

        $arr_multa = [];

        foreach ($deuda as $item) {
            $arr_multa[] = $item['multa_id'];
        }

        return $arr_multa;
    }

    public function prepararDetallePrePago(string $placa)
    {
        $array_multas = $this->obtenerDeudaArray($placa);

        $str_array_multas = implode(',', $array_multas);

        return $str_array_multas;
    }

    public function pagarMultas(string $pago_id, string $detalle_multa)
    {
        if (strlen($detalle_multa) > 3) {
            $array_multas = explode(',', $detalle_multa);

            foreach ($array_multas as $multa_id) {
                $this->_multaTransitoRepository->pagarMulta($multa_id, $pago_id);
            }
        }
    }
}