<?php

namespace App\Repositories\DerechoAseo;

use App\Interfaces\DerechoAseo\CuotaAseoRepositoryInterface;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class CuotaAseoRepository implements CuotaAseoRepositoryInterface
{
    public function obtenerDatosPropietarioCuotaAseo(int $rol, int $roldv): ?stdClass
    {
        return DB::table('da_aseo')
        ->join('da_cuota', function (JoinClause $join) {
            $join->on('da_aseo.ano', '=', 'da_cuota.ano')
            ->on('da_aseo.rol', '=', 'da_cuota.rol')
            ->on('da_aseo.rol_dv', '=', 'da_cuota.rol_dv');
        })
        ->where('da_aseo.rol', $rol)
        ->where('da_aseo.rol_dv', $roldv)
        ->orderBy('da_aseo.ano', 'desc')
        ->select('da_aseo.*')
        ->first();
    }

    public function obtenerDeudaActual(int $rol, int $roldv): Collection
    {
        return DB::table('da_cuota')
        ->join('da_aseo', function (JoinClause $join) {
            $join->on('da_cuota.ano', '=', 'da_aseo.ano')
            ->on('da_cuota.rol', '=', 'da_aseo.rol')
            ->on('da_cuota.rol_dv', '=', 'da_aseo.rol_dv');
        })
        ->where('da_cuota.rol', $rol)
        ->where('da_cuota.rol_dv', $roldv)
        ->where('da_cuota.estado', 0)
        ->orderBy('da_cuota.ano', 'asc')
        ->orderBy('da_cuota.cuota', 'asc')
        ->get();
    }

    public function obtenerCuotaAseoPagadas(int $rol, int $roldv): Collection
    {
        return DB::table('da_cuota')
        ->join('da_aseo', function (JoinClause $join) {
            $join->on('da_cuota.ano', '=', 'da_aseo.ano')
            ->on('da_cuota.rol', '=', 'da_aseo.rol')
            ->on('da_cuota.rol_dv', '=', 'da_aseo.rol_dv');
        })
        ->join('da_web_pago', 'da_cuota.pago_id', '=', 'da_cuota.pago_id')
        ->where('da_cuota.rol', $rol)
        ->where('da_cuota.rol_dv', $roldv)
        ->where('da_cuota.estado', 1)
        ->orderBy('da_cuota.ano', 'asc')
        ->orderBy('da_cuota.cuota', 'asc')
        ->get();
    }

    public function obtenerCuotaAseoPagadasPorPagoid(int $rol, int $roldv, string $pago_id): Collection
    {
        return DB::table('da_cuota')
        ->join('da_aseo', function (JoinClause $join) {
            $join->on('da_cuota.ano', '=', 'da_aseo.ano')
            ->on('da_cuota.rol', '=', 'da_aseo.rol')
            ->on('da_cuota.rol_dv', '=', 'da_aseo.rol_dv');
        })
        ->join('da_web_pago', 'da_cuota.pago_id', '=', 'da_cuota.pago_id')
        ->where('da_cuota.rol', $rol)
        ->where('da_cuota.rol_dv', $roldv)
        ->where('da_cuota.pago_id', $pago_id)
        ->where('da_cuota.estado', 1)
        ->orderBy('da_cuota.ano', 'asc')
        ->orderBy('da_cuota.cuota', 'asc')
        ->get();
    }

    public function obtenerPagosNoCentralizados(): Collection
    {
        return DB::table('da_cuota')
        ->join('da_aseo', function (JoinClause $join) {
            $join->on('da_cuota.ano', '=', 'da_aseo.ano')
            ->on('da_cuota.rol', '=', 'da_aseo.rol')
            ->on('da_cuota.rol_dv', '=', 'da_aseo.rol_dv');
        })
        ->join('da_web_pago', 'da_cuota.pago_id', '=', 'da_cuota.pago_id')
        ->where('da_cuota.estado', 1)
        ->where('da_cuota.estado_transferencia', '<>', 'PE')
        ->orderBy('da_cuota.pago_id', 'asc')
        ->limit(20)
        ->get();
    }

    public function actualizarEmail(int $rol, int $roldv, string $email): bool
    {
        return DB::table('da_aseo')
        ->where('rol', $rol)
        ->where('rol_dv', $roldv)
        ->update(['email_usuario' => $email]);
    }

    public function actualizarMontos(int $anio, int $rol, int $roldv, int $cuota, int $interes, int $multa): bool
    {
        return DB::table('da_cuota')
        ->where('ano', $anio)
        ->where('rol', $rol)
        ->where('rol_dv', $roldv)
        ->where('cuota', $cuota)
        ->update(['interes_pagado' => $interes, 'multa_pagado' => $multa]);
    }

    public function pagarCuotaAseo(int $anio, int $rol, int $roldv, int $cuota, string $pago_id, int $fecha_pago): bool
    {
        return DB::table('da_cuota')
        ->where('ano', $anio)
        ->where('rol', $rol)
        ->where('rol_dv', $roldv)
        ->where('cuota', $cuota)
        ->update(['estado' => 1, 'pago_id' => $pago_id]);
    }

    public function guardarComprobantePdf(int $anio, int $rol, int $roldv, int $cuota, string $pago_id, string $pdf): bool
    {
        return DB::table('da_cuota')
        ->where('ano', $anio)
        ->where('rol', $rol)
        ->where('rol_dv', $roldv)
        ->where('cuota', $cuota)
        ->where('pago_id', $pago_id)
        ->update(['doc_descar' => $pdf]);
    }

    public function centralizarCuotaAseo(int $anio, int $rol, int $roldv, int $cuota, string $pago_id): bool
    {
        return DB::table('da_cuota')
        ->where('ano', $anio)
        ->where('rol', $rol)
        ->where('rol_dv', $roldv)
        ->where('cuota', $cuota)
        ->where('pago_id', $pago_id)
        ->update(['estado_transferencia' => 'PE']);
    }
}