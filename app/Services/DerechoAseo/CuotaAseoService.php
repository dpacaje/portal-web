<?php

namespace App\Services\DerechoAseo;

use App\DTOs\DerechoAseo\ObtenerDeudaDTO;
use App\Interfaces\DerechoAseo\CuotaAseoRepositoryInterface;
use App\Interfaces\ParametrosGenerales\ParametrosGeneralesRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CuotaAseoService
{
    public function __construct(
        protected readonly CuotaAseoRepositoryInterface $_cuotaAseoRepository,
        protected readonly ParametrosGeneralesRepositoryInterface $_parametrosGeneralesRepository,
    ) {}

    public function obtenerPropietario(ObtenerDeudaDTO $data)
    {
        return $this->_cuotaAseoRepository->obtenerDatosPropietarioCuotaAseo($data->rol, $data->roldv);
    }

    public function obtenerDeuda(ObtenerDeudaDTO $data)
    {
        try {
            $deuda = $this->_cuotaAseoRepository->obtenerDeudaActual($data->rol, $data->roldv);

            if ($deuda->isEmpty()) {
                throw new \Exception('No se econtró deuda.');
            }

            DB::connection('mysql')->transaction(function () use ($deuda, $data) {
                foreach ($deuda as $item) {
                    $valores_calculados = $this->calcularIpc($item->fec_vcto, $item->valor_cuota);
    
                    if (is_null($valores_calculados)) {
                        throw new \Exception("No se pudo calcular los intereses y multas de {$item->ano}_{$item->rol}_{$item->rol_dv}_{$item->cuota}");
                    }
    
                    if ($item->interes_pagado != $valores_calculados['interes'] || $item->multa_pagado != $valores_calculados['multa']) {
                        $this->_cuotaAseoRepository->actualizarMontos($item->ano, $item->rol, $item->rol_dv, $item->cuota, $valores_calculados['interes'], $valores_calculados['multa']);
                    }
                }

                $this->_cuotaAseoRepository->actualizarEmail($data->rol, $data->roldv, $data->email);
            });

            $deuda = $this->_cuotaAseoRepository->obtenerDeudaActual($data->rol, $data->roldv);

            return $deuda;
        } catch (\Throwable $e) {
            Log::error('CuotaAseoService::obtenerDeuda', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function calcularIpc(int $fecha_vencimiento, int $valor)
    {
        try {
            $anio_vencimiento = substr($fecha_vencimiento, 0, 4);
            $mes_vencimiento = substr($fecha_vencimiento, 4, -2);

            $fecha_actual = date('Ymd');
            $anio_actual = date('Y');
            $mes_actual = date('n');

            if ($fecha_vencimiento > $fecha_actual) {
                return [
                    'neto' => $valor,
                    'interes' => 0,
                    'multa' => 0,
                    'total' => $valor
                ];
            }

            $data_ipc = $this->_parametrosGeneralesRepository->getIpc($anio_actual, $mes_actual, $anio_vencimiento);

            if (!$data_ipc) {
                return null;
            }

            $factor_ipc = 0;
            $factor_multa = 0;

            if ($mes_vencimiento == 1) {
                $factor_ipc = ($data_ipc->ipc_ene / 100);
                $factor_multa = ($data_ipc->interes_ene / 100);
            } else if ($mes_vencimiento == 2) {
                $factor_ipc = ($data_ipc->ipc_feb / 100);
                $factor_multa = ($data_ipc->interes_feb / 100);
            } else if ($mes_vencimiento == 3) {
                $factor_ipc = ($data_ipc->ipc_mar / 100);
                $factor_multa = ($data_ipc->interes_mar / 100);
            } else if ($mes_vencimiento == 4) {
                $factor_ipc = ($data_ipc->ipc_abr / 100);
                $factor_multa = ($data_ipc->interes_abr / 100);
            } else if ($mes_vencimiento == 5) {
                $factor_ipc = ($data_ipc->ipc_may / 100);
                $factor_multa = ($data_ipc->interes_may / 100);
            } else if ($mes_vencimiento == 6) {
                $factor_ipc = ($data_ipc->ipc_jun / 100);
                $factor_multa = ($data_ipc->interes_jun / 100);
            } else if ($mes_vencimiento == 7) {
                $factor_ipc = ($data_ipc->ipc_jul / 100);
                $factor_multa = ($data_ipc->interes_jul / 100);
            } else if ($mes_vencimiento == 8) {
                $factor_ipc = ($data_ipc->ipc_ago / 100);
                $factor_multa = ($data_ipc->interes_ago / 100);
            } else if ($mes_vencimiento == 9) {
                $factor_ipc = ($data_ipc->ipc_sep / 100);
                $factor_multa = ($data_ipc->interes_sep / 100);
            } else if ($mes_vencimiento == 10) {
                $factor_ipc = ($data_ipc->ipc_oct / 100);
                $factor_multa = ($data_ipc->interes_oct / 100);
            } else if ($mes_vencimiento == 11) {
                $factor_ipc = ($data_ipc->ipc_nov / 100);
                $factor_multa = ($data_ipc->interes_nov / 100);
            } else if ($mes_vencimiento == 12) {
                $factor_ipc = ($data_ipc->ipc_dic / 100);
                $factor_multa = ($data_ipc->interes_dic / 100);
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
            Log::error('CuotaAseoService::calcularIpc', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}