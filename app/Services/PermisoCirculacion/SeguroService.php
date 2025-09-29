<?php

namespace App\Services\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\SeguroRepositoryInterface;
use App\Libraries\Seguro;

class SeguroService
{
    public function __construct(
        protected readonly SeguroRepositoryInterface $_seguroRepositoryInterface,
        protected readonly Seguro $_seguroWebService,
    ) {}

    public function obtenerSeguroWS(string $placa)
    {
        $seguro = $this->_seguroWebService->getSoap($placa);

        if (!isset($seguro['status'])) {
            throw new \Exception('No se pudo obtener SEGURO SOAP');
        }

        return $seguro;
    }

    public function verificarSeguro(int $ano_cargo, string $placa, int $tipo_cargo, int $id_tipo_vehiculo)
    {
        if (!config('envar.soap_enable')) {
            return true;
        }

        $anio_actual = date('Y');
        $anio_anterior = date('Y')-1;
        $anio_siguiente = date('Y')+1;
        $fecha = date('Ymd');

        if ($ano_cargo < $anio_anterior) {
            return true;
        } else {
            if ($tipo_cargo == 2) {
                return true;
            } else {
                if ($id_tipo_vehiculo == 1) {
                    if ($ano_cargo == $anio_actual) {
                        $fecha = $anio_siguiente . '0331';
                    } elseif($ano_cargo == $anio_anterior) {
                        $fecha = $anio_actual . '0331';
                    }
                } elseif($id_tipo_vehiculo == 2) {
                    if ($ano_cargo == $anio_actual) {
                        $fecha = $anio_siguiente . '0531';
                    } elseif($ano_cargo == $anio_anterior) {
                        $fecha = $anio_actual . '0531';
                    }
                } elseif($id_tipo_vehiculo == 3) {
                    if ($ano_cargo == $anio_actual) {
                        $fecha = $anio_siguiente . '0930';
                    } elseif($ano_cargo == $anio_anterior) {
                        $fecha = $anio_actual . '0930';
                    }
                }
            }
        }

        $estado_soap = false;

        $seguro = $this->obtenerSeguro($anio_actual, $placa, $fecha);

        if (!is_null($seguro)) {
            $estado_soap = true;
        } else {
            $soap_xml = $this->obtenerSeguroWS($placa);

            if ($soap_xml['status'] === true) {
                if ($soap_xml['FechaTermino'] >= $fecha) {
                    $estado_soap = true;
                } else {
                    $estado_soap = false;
                }
                $this->_seguroRepositoryInterface->registrarSeguro($placa, $soap_xml['code'], $soap_xml['NombreCompania'], $soap_xml['NroPoliza'], $soap_xml['FechaInicio'], $soap_xml['FechaTermino'], 'PORTAL');
            } else {
                $estado_soap = false;
            }
        }

        return $estado_soap;
    }

    public function obtenerSeguro(int $anio_actual, string $placa, int $fecha_vencimiento)
    {
        return $this->_seguroRepositoryInterface->obtenerSeguroValido($anio_actual, $placa, $fecha_vencimiento);
    }

    public function obtenerUltimoSeguro(string $placa)
    {
        return $this->_seguroRepositoryInterface->obtenerUltimoSeguro($placa);
    }

    public function insertarSeguroAdmin(string $placa, string $code, string $nombre_compania, string $nro_poliza, string $fecha_inicio, string $fecha_termino, string $responsable)
    {
        $this->_seguroRepositoryInterface->eliminarSeguro($placa);
        return $this->_seguroRepositoryInterface->registrarSeguro($placa, $code, $nombre_compania, $nro_poliza, $fecha_inicio, $fecha_termino, $responsable);
    }
}