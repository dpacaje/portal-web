<?php

namespace App\Services\PermisoCirculacion;

use App\DTOs\PermisoCirculacion\ObtenerDeudaDTO;
use App\Interfaces\ParametrosGenerales\ParametrosGeneralesRepositoryInterface;
use App\Interfaces\PermisoCirculacion\MaestroPermisoRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MaestroPermisoService
{
    public function __construct(
        protected MaestroPermisoRepositoryInterface $_maestroPermisoRepository,
        protected ParametrosGeneralesRepositoryInterface $_parametrosGeneralesRepository,
    ) {}

    public function obtenerDeuda(ObtenerDeudaDTO $data)
    {
        try {
            $arr_rut = separar_rut($data->rut);

            $anterior = $this->_maestroPermisoRepository->obtenerDeudaAnterior($arr_rut[0], $arr_rut[1], $data->placa);
            $actual = $this->_maestroPermisoRepository->obtenerDeudaActual($arr_rut[0], $arr_rut[1], $data->placa);
            $deuda = $anterior->merge($actual);

            if ($deuda->isEmpty()) {
                throw new \Exception('No se econtró deuda.');
            }

            foreach ($deuda as $item) {
                $intereses = $this->calcularIpc($item->ano_cargo, $item->tipo_cargo, $item->id_tipo_vehiculo, $item->pago_monto_neto);

                if (is_null($intereses)) {
                    throw new \Exception('No se pudo calcular los intereses y multas.');
                }
                dd($intereses);
            }

            $anterior = $this->_maestroPermisoRepository->obtenerDeudaAnterior($arr_rut[0], $arr_rut[1], $data->placa);
            $actual = $this->_maestroPermisoRepository->obtenerDeudaActual($arr_rut[0], $arr_rut[1], $data->placa);
            $deuda = $anterior->merge($actual);

            return $deuda;
        } catch (\Exception $e) {
            Log::error('MaestroPermisoService::obtenerDeuda', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function calcularIpc(int $anio_cargo, int $tipo_cargo, int $id_tipo_vehiculo, int $valor)
    {
        try {
            $anio_actual = date('Y');
            $mes_actual = date('n');

            $data_ipc = $this->_parametrosGeneralesRepository->getIpc($anio_actual, $mes_actual, $anio_cargo);

            if (!$data_ipc) {
                return null;
            }

            $factor_ipc = 0;
            $factor_multa = 0;

            if ($id_tipo_vehiculo == 1) {
                if ($tipo_cargo == 0) {
                    $factor_ipc = ($data_ipc->ipc_mar / 100);
                    $factor_multa = ($data_ipc->interes_mar / 100);
                } else if ($tipo_cargo == 1) {
                    $factor_ipc = ($data_ipc->ipc_mar / 100);
                    $factor_multa = ($data_ipc->interes_mar / 100);
                } else if ($tipo_cargo == 2) {
                    $factor_ipc = ($data_ipc->ipc_ago / 100);
                    $factor_multa = ($data_ipc->interes_ago / 100);
                } else {
                    return null;
                }
            } else if ($id_tipo_vehiculo == 2) {
                if ($tipo_cargo == 0) {
                    $factor_ipc = ($data_ipc->ipc_may / 100);
                    $factor_multa = ($data_ipc->interes_may / 100);
                } else if ($tipo_cargo == 1) {
                    $factor_ipc = ($data_ipc->ipc_may / 100);
                    $factor_multa = ($data_ipc->interes_may / 100);
                } else if ($tipo_cargo == 2) {
                    $factor_ipc = ($data_ipc->ipc_jun / 100);
                    $factor_multa = ($data_ipc->interes_jun / 100);
                } else {
                    return null;
                }
            } else if ($id_tipo_vehiculo == 3) {
                if ($tipo_cargo == 0) {
                    $factor_ipc = ($data_ipc->ipc_sep / 100);
                    $factor_multa = ($data_ipc->interes_sep / 100);
                } else if ($tipo_cargo == 1) {
                    $factor_ipc = ($data_ipc->ipc_sep / 100);
                    $factor_multa = ($data_ipc->interes_sep / 100);
                } else if ($tipo_cargo == 2) {
                    $factor_ipc = ($data_ipc->ipc_oct / 100);
                    $factor_multa = ($data_ipc->interes_oct / 100);
                } else {
                    return null;
                }
            } else {
                return null;
            }

            $interes = round($valor * $factor_ipc);
            $multa = round(($valor + ($valor * $factor_ipc)) * $factor_multa);

            return [
                'interes' => $interes,
                'multa' => $multa,
            ];
        } catch(\Exception $e) {
            Log::error('MaestroPermisoService::calcularIpc', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}