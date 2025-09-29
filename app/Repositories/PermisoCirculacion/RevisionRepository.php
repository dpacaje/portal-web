<?php

namespace App\Repositories\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\RevisionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use stdClass;

class RevisionRepository implements RevisionRepositoryInterface
{
    public function obtenerRevisionValida(int $ano_min, int $ano_max, string $placa, string $fecha_actual): ?stdClass
    {
        return DB::table('pc_ministerio_transporte')
        ->where('placa', $placa)
        ->whereBetween('ano', [$ano_min, $ano_max])
        ->whereBetween(DB::raw("CAST(SUBSTRING_INDEX(fecha_vencimiento, '-', -1) AS UNSIGNED)"), [$ano_min, $ano_max])
        ->where(DB::raw("STR_TO_DATE(fecha_vencimiento, '%d-%m-%Y')"), '>=', $fecha_actual)
        ->first();
    }

    public function obtenerUltimaRevision(string $placa): ?stdClass
    {
        return DB::table('pc_ministerio_transporte')
        ->where('placa', $placa)
        ->where('placa', $placa)
        ->orderBy("STR_TO_DATE(fecha_vencimiento, '%d-%m-%Y')", 'desc')
        ->first();
    }

    public function registrarRevision(string $codigo, string $placa, string $cod_resultado, string $comuna, string $direccion, string $fecha_inicio, string $fecha_termino, string $id_crt, string $mensaje, string $nro_certificado, string $region, string $responsable): bool
    {
        return DB::table('pc_ministerio_transporte')->insert([
            'codigo_prt' => $codigo,
            'ano' => date('Y'),
            'placa' => $placa,
            'cod_resultado' => $cod_resultado,
            'comuna_prt' => $comuna,
            'direccion_prt' => $direccion,
            'fecha_revision' => $fecha_inicio,
            'fecha_vencimiento' => $fecha_termino,
            'id_crt' => $id_crt,
            'mensaje_resultado' => $mensaje,
            'numero_certificado' => $nro_certificado,
            'region_prt' => $region,
            'fechaconsulta' => date('Y-m-d H:i:s'),
            'homologado' => 0,
            'res_ingreso' => $responsable
        ]);
    }

    public function eliminarRevision(string $placa): bool
    {
        return DB::table('pc_ministerio_transporte')
        ->where('placa', $placa)
        ->delete();
    }
}