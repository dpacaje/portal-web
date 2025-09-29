<?php

namespace App\Repositories\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\SeguroRepositoryInterface;
use Illuminate\Support\Facades\DB;
use stdClass;

class SeguroRepository implements SeguroRepositoryInterface
{
    public function obtenerSeguroValido(int $ano, string $placa, int $fecha): ?stdClass
    {
        return DB::table('pc_seguros_soap')
        ->where('ano', $ano)
        ->where('placa', $placa)
        ->where('fec_venc', '>=', $fecha)
        ->orderBy('fec_venc', 'desc')
        ->first();
    }

    public function obtenerUltimoSeguro(string $placa): ?stdClass
    {
        return DB::table('pc_seguros_soap')
        ->where('ano', date('Y'))
        ->where('placa', $placa)
        ->orderBy('fec_venc', 'desc')
        ->first();
    }

    public function registrarSeguro(string $placa, string $code, string $nombre_compania, string $nro_poliza, string $fecha_inicio, string $fecha_termino, string $responsable): bool
    {
        return DB::table('pc_seguros_soap')->insert([
            'ano' => date('Y'),
            'placa' => $placa,
            'estado' => $code,
            'nom_comp' => $nombre_compania,
            'poliza' => $nro_poliza,
            'fec_otor' => $fecha_inicio,
            'fec_venc' => $fecha_termino,
            'resp_ingreso' => $responsable,
            'fecha_sistema' => date('Y-m-d H:i:s')
        ]);
    }

    public function eliminarSeguro(string $placa): bool
    {
        return DB::table('pc_seguros_soap')
        ->where('placa', $placa)
        ->delete();
    }
}