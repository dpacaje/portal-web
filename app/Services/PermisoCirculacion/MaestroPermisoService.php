<?php

namespace App\Services\PermisoCirculacion;

use App\DTOs\PermisoCirculacion\ConfirmacionDeudaDTO;
use App\DTOs\PermisoCirculacion\ObtenerDeudaDTO;
use App\Interfaces\ParametrosGenerales\ParametrosGeneralesRepositoryInterface;
use App\Interfaces\PermisoCirculacion\MaestroPermisoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaestroPermisoService
{
    public function __construct(
        protected readonly MaestroPermisoRepositoryInterface $_maestroPermisoRepository,
        protected readonly ParametrosGeneralesRepositoryInterface $_parametrosGeneralesRepository,
    ) {}

    public function obtenerPropietario(ObtenerDeudaDTO $data)
    {
        return $this->_maestroPermisoRepository->obtenerDatosPropietarioVehiculo($data->rutn, $data->rutdv, $data->placa);
    }

    public function obtenerDeuda(ObtenerDeudaDTO $data)
    {
        try {
            $anterior = $this->_maestroPermisoRepository->obtenerDeudaAnterior($data->rutn, $data->rutdv, $data->placa);
            $actual = $this->_maestroPermisoRepository->obtenerDeudaActual($data->rutn, $data->rutdv, $data->placa);
            $deuda = $anterior->merge($actual);

            if ($deuda->isEmpty()) {
                throw new \Exception('No se econtró deuda.');
            }

            DB::connection('mysql')->transaction(function () use ($deuda, $data) {
                foreach ($deuda as $item) {
                    $neto = ($item->pago_monto_neto + $item->pago_correccion);
                    $valores_calculados = $this->calcularIpc($item->ano_cargo, $item->tipo_cargo, $item->id_tipo_vehiculo, $neto);
    
                    if (is_null($valores_calculados)) {
                        throw new \Exception("No se pudo calcular los intereses y multas de {$item->ano_cargo}_{$item->placa_veh}_{$item->tipo_cargo}");
                    }
    
                    if ($item->pago_interes != $valores_calculados['interes'] || $item->pago_multa != $valores_calculados['multa'] || $item->pago_total_calculado != $valores_calculados['total']) {
                        $this->_maestroPermisoRepository->actualizarMontos($item->ano_cargo, $item->placa_veh, $item->tipo_cargo, $valores_calculados['interes'], $valores_calculados['multa'], $valores_calculados['total']);
                    }
                }

                $this->_maestroPermisoRepository->actualizarEmail($data->rutn, $data->rutdv, $data->placa, $data->email);
            });

            $anterior = $this->_maestroPermisoRepository->obtenerDeudaAnterior($data->rutn, $data->rutdv, $data->placa);
            $actual = $this->_maestroPermisoRepository->obtenerDeudaActual($data->rutn, $data->rutdv, $data->placa);

            return [
                'anterior' => $anterior,
                'actual' => $actual,
            ];
        } catch (\Throwable $e) {
            Log::error('MaestroPermisoService::obtenerDeuda', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function calcularIpc(int $anio_cargo, int $tipo_cargo, int $id_tipo_vehiculo, int $valor)
    {
        try {
            $anio_actual = date('Y');
            $mes_actual = date('n');

            if ($anio_cargo > $anio_actual) {
                return [
                    'neto' => $valor,
                    'interes' => 0,
                    'multa' => 0,
                    'total' => $valor
                ];
            }

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

            $total = ($valor + $interes + $multa);

            return [
                'neto' => $valor,
                'interes' => $interes,
                'multa' => $multa,
                'total' => $total
            ];
        } catch(\Throwable $e) {
            Log::error('MaestroPermisoService::calcularIpc', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function obtenerUltimoPermisoDeuda(array $permisos): ?object
    {
        try {
            if (!$permisos['actual']) {
                $perm = end($permisos['anterior']);
            } else {
                $perm = reset($permisos['actual']);
            }

            return $perm[0] ?? null;
        } catch(\Throwable $e) {
            Log::error('MaestroPermisoService::obtenerUltimoPermisoDeuda', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function validarPermisosPrePago(int $monto_permiso, null|array $permisos_anteriores, null|string $permisos_actuales)
    {
        $subtotal = 0;

        if (is_null($permisos_anteriores) && is_null($permisos_actuales)) {
            return false;
        }

        if (!is_null($permisos_anteriores)) {
            foreach ($permisos_anteriores as $row) {
                $data = explode('_', $row);
                $p_monto = $data[0];
                $p_placa = $data[1];
                $p_tipocargo = $data[2];
                $p_aniocargo = $data[3];

                if ($p_monto < 100) {
                    return false;
                }

                $permiso = $this->_maestroPermisoRepository->obtenerDeudaPorMonto($p_aniocargo, $p_placa, $p_tipocargo, $p_monto);

                if (!$permiso) {
                    return false;
                } else {
                    if ($p_monto != $permiso->pago_total_calculado) {
                        return false;
                    } else {
                        $subtotal += $permiso->pago_total_calculado;
                    }
                }
            }
        }

        if (!is_null($permisos_actuales)) {
            $data = explode('_', $permisos_actuales);
            $p_monto = $data[0];
            $p_placa = $data[1];
            $p_tipocargo = $data[2];
            $p_aniocargo = $data[3];

            if ($p_monto < 100) {
                return false;
            }

            $permiso = $this->_maestroPermisoRepository->obtenerDeudaPorMonto($p_aniocargo, $p_placa, $p_tipocargo, $p_monto);

            if (!$permiso) {
                return false;
            } else {
                if ($p_monto != $permiso->pago_total_calculado) {
                    return false;
                } else {
                    $subtotal += $permiso->pago_total_calculado;
                }
            }
        }

        if ($monto_permiso != $subtotal) {
            return false;
        }

        return true;
    }

    public function prepararDetallePrePago(ConfirmacionDeudaDTO $data)
    {
        try {
            $str_permisos_anteriores = '';
            $str_permisos_actuales = '';

            if (!is_null($data->permiso_anterior)) {
                $str_permisos_anteriores = implode(',', $data->permiso_anterior);
            }

            if (!is_null($data->permiso_actual)) {
                $data = explode('_', $data->permiso_actual);
                $p_placa = $data[1];
                $p_tipocargo = $data[2];
                $p_aniocargo = $data[3];
                $str_permisos_actuales = $p_aniocargo . '_' . $p_tipocargo . '_' . $p_placa . ',';
            }

            return $str_permisos_actuales . $str_permisos_anteriores;
        } catch (\Throwable $e) {
            Log::error('MaestroPermisoService::procesarPrePago', [
                'file' => $e->getFile(),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function pagarPermisos(string $pago_id, string $detalle_permiso)
    {
        $array_permisos = explode(',', $detalle_permiso);
        $fecha_actual = date('Y');

        foreach ($array_permisos as $row) {
            $arr_perm = explode('_', $row);

            if (count($arr_perm) == 3) {
                $anio_cargo = $arr_perm[0];
                $tipo_cargo = $arr_perm[1];
                $placa = $arr_perm[2];

                $this->_maestroPermisoRepository->pagarPermiso($anio_cargo, $placa, $tipo_cargo, $pago_id, $fecha_actual);
                $this->pagarPermisosDif($anio_cargo, $placa, $tipo_cargo);
            } else if (count($arr_perm) == 4) {
                $anio_cargo = $arr_perm[3];
                $tipo_cargo = $arr_perm[2];
                $placa = $arr_perm[1];

                $this->_maestroPermisoRepository->pagarPermiso($anio_cargo, $placa, $tipo_cargo, $pago_id, $fecha_actual);
                $this->pagarPermisosDif($anio_cargo, $placa, $tipo_cargo);
            }
        }
    }

    public function pagarPermisosDif(int $anio_cargo, string $placa, int $tipo_cargo)
    {
        if ($tipo_cargo == 0) {
            $this->_maestroPermisoRepository->pagarPermisoDif($anio_cargo, $placa, 1, 4, 'pagado por total');
        } else if ($tipo_cargo == 1) {
            $this->_maestroPermisoRepository->pagarPermisoDif($anio_cargo, $placa, 0, 3, 'pagado por 1era cuota');
        }
    }
}