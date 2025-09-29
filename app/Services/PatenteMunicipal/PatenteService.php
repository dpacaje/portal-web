<?php

namespace App\Services\PatenteMunicipal;

use App\DTOs\PatenteMunicipal\ObtenerDeudaDTO;
use App\Interfaces\ParametrosGenerales\ParametrosGeneralesRepositoryInterface;
use App\Interfaces\PatenteMunicipal\PatenteRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PatenteService
{
    public function __construct(
        protected readonly PatenteRepositoryInterface $_patenteRepository,
        protected readonly ParametrosGeneralesRepositoryInterface $_parametrosGeneralesRepository,
    ) {}

    public function obtenerPropietario(ObtenerDeudaDTO $data)
    {
        return $this->_patenteRepository->obtenerDatosPropietarioPatente($data->rutn, $data->rutdv, $data->rol);
    }

    public function obtenerDeuda(ObtenerDeudaDTO $data)
    {
        try {
            $deuda = $this->_patenteRepository->obtenerDeudaActual($data->rutn, $data->rutdv, $data->rol);

            if ($deuda->isEmpty()) {
                throw new \Exception('No se econtró deuda.');
            }

            DB::connection('mysql')->transaction(function () use ($deuda, $data) {
                foreach ($deuda as $item) {
                    $valores_calculados = $this->calcularIpc($item->ano, $item->sem, $item->total);
    
                    if (is_null($valores_calculados)) {
                        throw new \Exception("No se pudo calcular los intereses y multas de {$item->ano}_{$item->sem}_{$item->rol}");
                    }
    
                    if ($item->interes != $valores_calculados['interes'] || $item->multa != $valores_calculados['multa']) {
                        $this->_patenteRepository->actualizarMontos($item->ano, $item->sem, $item->rol, $valores_calculados['interes'], $valores_calculados['multa'], $valores_calculados['total']);
                    }
                }

                $this->_patenteRepository->actualizarEmail($data->rutn, $data->rutdv, $data->rol, $data->email);
            });

            $deuda = $this->_patenteRepository->obtenerDeudaActual($data->rutn, $data->rutdv, $data->rol);

            return $deuda;
        } catch (\Throwable $e) {
            Log::error('PatenteService::obtenerDeuda', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function calcularIpc(int $anio, int $sem, int $valor)
    {
        try {
            $anio_actual = date('Y');
            $mes_actual = date('n');

            if ($anio > $anio_actual) {
                return [
                    'neto' => $valor,
                    'interes' => 0,
                    'multa' => 0,
                    'total' => $valor
                ];
            }

            $data_ipc = $this->_parametrosGeneralesRepository->getIpc($anio_actual, $mes_actual, $anio);

            if (!$data_ipc) {
                return null;
            }

            $factor_ipc = 0;
            $factor_multa = 0;

            if ($sem == 1) {
                $factor_ipc = ($data_ipc->ipc_ene / 100);
                $factor_multa = ($data_ipc->interes_ene / 100);
            } else if ($sem == 2) {
                $factor_ipc = ($data_ipc->ipc_jul / 100);
                $factor_multa = ($data_ipc->interes_jul / 100);
            } else {
                return null;
            }

            $interes = round($valor * $factor_ipc);
            $multa = round(($valor + ($valor * $factor_ipc)) * $factor_multa);

            $total = ($valor + $interes + $multa);

            return [
                'neto' => $valor,
                'interes' => $interes,
                'multa' => $multa,
                'total' => $total
            ];
        } catch(\Throwable $e) {
            Log::error('PatenteService::calcularIpc', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}