<?php

namespace App\Repositories\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\MaestroPermisoRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class MaestroPermisoRepository implements MaestroPermisoRepositoryInterface
{
    public function obtenerDatosPropietarioVehiculo(int $rut, string $dv, string $placa): ?stdClass
    {
        return DB::table('pc_maestro_permisos')
        ->where('placa_veh', $placa)
        ->where('prop_rut', $rut)
        ->where('prop_rut_dv', $dv)
        ->orderBy('ano_cargo', 'desc')
        ->orderBy('tipo_cargo', 'desc')
        ->first();
    }

    public function obtenerDeudaActual(int $rut, string $dv, string $placa): Collection
    {
        return DB::table('pc_maestro_permisos')
        ->join('pc_configuracion_fechas_portales', 'pc_maestro_permisos.tipo_cargo', '=', 'pc_configuracion_fechas_portales.periodo')
        ->where('pc_maestro_permisos.ano_cargo', date('Y'))
        ->where('pc_maestro_permisos.placa_veh', $placa)
        ->where('pc_maestro_permisos.estado', 0)
        ->where('pc_maestro_permisos.prop_rut', $rut)
        ->where('pc_maestro_permisos.prop_rut_dv', $dv)
        ->where('pc_configuracion_fechas_portales.fecha_apagado', '>=', date('Ymd'))
        ->orderBy('pc_maestro_permisos.ano_cargo', 'asc')
        ->orderBy('pc_maestro_permisos.tipo_cargo', 'asc')
        ->get();
    }

    public function obtenerDeudaAnterior(int $rut, string $dv, string $placa): Collection
    {
        return DB::table('pc_maestro_permisos')
        ->where('ano_cargo', '<', date('Y'))
        ->where('placa_veh', $placa)
        ->where('tipo_cargo', '!=', 1)
        ->where('estado', 0)
        ->where('prop_rut', $rut)
        ->where('prop_rut_dv', $dv)
        ->orderBy('ano_cargo', 'asc')
        ->orderBy('tipo_cargo', 'asc')
        ->get();
    }

    public function obtenerPermisosPagados(int $rut, string $dv, string $placa): Collection
    {
        return DB::table('pc_maestro_permisos')
        ->from('pc_maestro_permisos mp')
        ->join('pc_web_pago wp', 'wp.pago_id = mp.id_pago')
        ->where('mp.placa_veh', $placa)
        ->where('mp.id_pago !=', 0)
        ->where('mp.estado', 1)
        ->where('mp.prop_rut', $rut)
        ->where('mp.prop_rut_dv', $dv)
        ->orderBy('mp.ano_cargo ASC')
        ->orderBy('mp.tipo_cargo ASC')
        ->get();
    }

    public function obtenerPermisosPagadosPorPagoid(string $placa, string $pago_id): Collection
    {
        return DB::table('pc_maestro_permisos')
        ->select('*')
        ->from('pc_maestro_permisos mp')
        ->join('pc_web_pago wp', 'wp.pago_id = mp.id_pago')
        ->where('mp.placa_veh', $placa)
        ->where('mp.id_pago', $pago_id)
        ->where('mp.estado', 1)
        ->orderBy('mp.ano_cargo ASC')
        ->orderBy('mp.tipo_cargo ASC')
        ->get();
    }

    public function obtenerPagosNoCentralizados(): Collection
    {
        return DB::table('pc_maestro_permisos')
        ->select('*')
        ->from('pc_maestro_permisos mp')
        ->join('pc_web_pago wp', 'wp.pago_id = mp.id_pago')
        ->where('mp.id_pago !=', 0)
        ->where('mp.estado', 1)
        ->where('mp.estado_transferencia !=', 'PE')
        ->orderBy('wp.pago_id ASC')
        ->limit(20)
        ->get();
    }

    public function actualizarEmail(int $rut, string $dv, string $placa, string $email): bool
    {
        return DB::table('pc_maestro_permisos')
        ->where('placa_veh', $placa)
        ->where('estado', 0)
        ->where('prop_rut', $rut)
        ->where('prop_rut_dv', $dv)
        ->update(['prop_correo' => $email]);
    }

    public function actualizarMontos(int $anio, string $placa, int $tipo_cargo, int $interes, int $multa, int $total): bool
    {
        return DB::table('pc_maestro_permisos')
        ->where('ano_cargo', $anio)
        ->where('placa_veh', $placa)
        ->where('tipo_cargo', $tipo_cargo)
        ->update(['pago_interes' => $interes, 'pago_multa' => $multa, 'pago_total_calculado' => $total]);
    }

    public function pagarPermiso(int $anio, string $placa, int $tipo_cargo, string $pago_id, int $fecha_pago): bool
    {
        return DB::table('pc_maestro_permisos')
        ->where('ano_cargo', $anio)
        ->where('placa_veh', $placa)
        ->where('tipo_cargo', $tipo_cargo)
        ->update(['id_pago' => $pago_id, 'estado' => 1, 'estado_descrip' => 'PAGADO']);
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