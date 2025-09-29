<?php

namespace App\Services\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\RevisionRepositoryInterface;
use App\Libraries\Revision;

class RevisionService
{
    public function __construct(
        protected readonly RevisionRepositoryInterface $_revisionRepositoryInterface,
        protected readonly Revision $_revisionWebService,
    ) {}

    public function obtenerRevisionWS(string $placa)
    {
        $revision = $this->_revisionWebService->getRevision($placa);

        if (!isset($revision['status'])) {
            throw new \Exception('No se pudo obtener REVISION TECNICA');
        }

        return $revision;
    }

    public function verificarRevision(int $ano_cargo, string $placa, int $tipo_cargo)
    {
        if (!config('envar.mtt_enable')) {
            return true;
        }

        $anio_minimo = date('Y')-3;
        $anio_maximo = date('Y')+3;
        $anio_anterior = date('Y')-1;
        $fecha = date('Y-m-d H:i:s');
        $fecha_validacion = date('d-m-Y');

        if ($ano_cargo < $anio_anterior) {
            return [true, $placa, 'Revision Habilitada.'];
        } else {
            if ($tipo_cargo == 2) {
                return [true, $placa, 'Revision Habilitada.'];
            }
        }

        $estado_revision = false;

        $revision = $this->_revisionRepositoryInterface->obtenerRevisionValida($anio_minimo, $anio_maximo, $placa, $fecha);

        if (!is_null($revision)) {
            $estado_revision = true;
        } else {
            $mtt_xml = $this->obtenerRevisionWS($placa);

            if (isset($mtt_xml['data']['revision_tecnica']['COD_RESULTADO'])) {
                if ($mtt_xml['data']['revision_tecnica']['COD_RESULTADO'] == 'R') {
                    $estado_revision = false;
                } else {
                    $fecha_ministerio = strtotime($mtt_xml['data']['revision_tecnica']['FECHA_VENCIMIENTO']);
                    $fecha_validacion = strtotime($fecha_validacion);
                    if ($fecha_ministerio >= $fecha_validacion) {
                        $estado_revision = true;
                    } else {
                        $estado_revision = false;
                    }
                }
                $this->_revisionRepositoryInterface->registrarRevision($mtt_xml['data']['revision_tecnica']['CODIGO_PRT'], $placa, $mtt_xml['data']['revision_tecnica']['COD_RESULTADO'], $mtt_xml['data']['revision_tecnica']['COMUNA_PRT'], $mtt_xml['data']['revision_tecnica']['DIRECCION_PRT'], $mtt_xml['data']['revision_tecnica']['FECHA_REVISION'], $mtt_xml['data']['revision_tecnica']['FECHA_VENCIMIENTO'], $mtt_xml['data']['revision_tecnica']['ID_CRT'], $mtt_xml['data']['revision_tecnica']['MENSAJE_RESULTADO'], $mtt_xml['data']['revision_tecnica']['NUMERO_CERTIFICADO'], $mtt_xml['data']['revision_tecnica']['REGION_PRT'], 'PORTAL');
            } else {
                $estado_revision = false;
            }
        }

        return $estado_revision;
    }

    public function obtenerUltimaRevision(string $placa)
    {
        return $this->_revisionRepositoryInterface->obtenerUltimaRevision($placa);
    }

    public function insertarRevisionAdmin(string $codigo, string $placa, string $cod_resultado, string $comuna, string $direccion, string $fecha_inicio, string $fecha_termino, string $id_crt, string $mensaje, string $nro_certificado, string $region, string $responsable)
    {
        $this->_revisionRepositoryInterface->eliminarRevision($placa);
        return $this->_revisionRepositoryInterface->registrarRevision($codigo, $placa, $cod_resultado, $comuna, $direccion, $fecha_inicio, $fecha_termino, $id_crt, $mensaje, $nro_certificado, $region, $responsable);
    }
}