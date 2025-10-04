<?php

namespace App\Repositories\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\WebPagoRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class WebPagoRepository implements WebPagoRepositoryInterface
{
    public function obtenerUltimosIntentosPagos(string $placa, int $fecha_limite): Collection
    {
        return DB::table('pc_web_pago')
        ->select('pago_id, token')
        ->where('estado', 'Creado')
        ->where('placa_gral', $placa)
        ->where('LEFT(fecha_stamp, 8)', '>=', $fecha_limite)
        ->where('LENGTH(token)', '>', $fecha_limite)
        ->orderBy('pago_id', 'desc')
        ->limit(3)
        ->get();
    }

    public function obtenerUltimoPagoId(string $pago_id): stdclass
    {
        return DB::table('pc_web_pago')
        ->select('pago_id')
        ->where('pago_id', 'LIKE', $pago_id . '%')
        ->orderBy('pago_id', 'desc')
        ->first();
    }

    public function pagarPermisoDif(int $anio, string $placa, int $tipo_cargo, int $estado, string $descripcion): bool
    {
        return DB::table('pc_maestro_permisos')
        ->where('ano_cargo', $anio)
        ->where('placa_veh', $placa)
        ->where('tipo_cargo', $tipo_cargo)
        ->update(['estado' => $estado, 'estado_descrip' => $descripcion]);
    }

    public function centralizarPermiso(int $anio, string $placa, int $tipo_cargo, string $pago_id): bool
    {
        return DB::table('pc_maestro_permisos')
        ->where('ano_cargo', $anio)
        ->where('placa_veh', $placa)
        ->where('tipo_cargo', $tipo_cargo)
        ->where('id_pago', $pago_id)
        ->update(['estado_transferencia' => 'PE']);
    }
}