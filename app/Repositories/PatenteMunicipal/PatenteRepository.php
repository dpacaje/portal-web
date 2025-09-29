<?php

namespace App\Repositories\PatenteMunicipal;

use App\Interfaces\PatenteMunicipal\PatenteRepositoryInterface;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class PatenteRepository implements PatenteRepositoryInterface
{
    public function obtenerDatosPropietarioPatente(int $rut, string $dv, int $rol): ?stdClass
    {
        return DB::table('pa_patentes')
        ->join('pa_pagos', function ($join) {
            $join->on('pa_patentes.ano', '=', 'pa_pagos.ano')
            ->on('pa_patentes.sem', '=', 'pa_pagos.sem')
            ->on('pa_patentes.rol', '=', 'pa_pagos.rol');
        })
        ->where('pa_patentes.rol', $rol)
        ->where('pa_patentes.rut', $rut)
        ->where('pa_patentes.dv', $dv)
        ->orderBy('pa_patentes.ano', 'desc')
        ->orderBy('pa_patentes.sem', 'desc')
        ->first();
    }

    public function obtenerDeudaActual(int $rut, string $dv, int $rol): Collection
    {
        return DB::table('pa_patentes')
        ->join('pa_pagos', function (JoinClause $join) {
            $join->on('pa_patentes.ano', '=', 'pa_pagos.ano')
            ->on('pa_patentes.sem', '=', 'pa_pagos.sem')
            ->on('pa_patentes.rol', '=', 'pa_pagos.rol');
        })
        ->where('pa_patentes.rol', $rol)
        ->where('pa_patentes.rut', $rut)
        ->where('pa_patentes.dv', $dv)
        ->where('pa_pagos.situacion', 0)
        ->orderBy('pa_patentes.ano', 'asc')
        ->orderBy('pa_patentes.sem', 'asc')
        ->get();
    }

    public function obtenerPatentesPagadas(int $rut, string $dv, int $rol): Collection
    {
        return DB::table('pa_patentes')
        ->join('pa_pagos', function ($join) {
            $join->on('pa_patentes.ano', '=', 'pa_pagos.ano')
            ->on('pa_patentes.sem', '=', 'pa_pagos.sem')
            ->on('pa_patentes.rol', '=', 'pa_pagos.rol');
        })
        ->join('web_pago', 'pa_patentes.pago_id', '=', 'web_pago.pago_id')
        ->where('pa_patentes.rol', $rol)
        ->where('pa_patentes.rut', $rut)
        ->where('pa_patentes.dv', $dv)
        ->where('pa_pagos.situacion', 1)
        ->orderBy('pa_patentes.ano', 'asc')
        ->orderBy('pa_patentes.sem', 'asc')
        ->get();
    }

    public function obtenerPatentesPagadasPorPagoid(int $rol, string $pago_id): Collection
    {
        return DB::table('pa_patentes')
        ->join('pa_pagos', function ($join) {
            $join->on('pa_patentes.ano', '=', 'pa_pagos.ano')
            ->on('pa_patentes.sem', '=', 'pa_pagos.sem')
            ->on('pa_patentes.rol', '=', 'pa_pagos.rol');
        })
        ->join('web_pago', 'pa_patentes.pago_id', '=', 'web_pago.pago_id')
        ->where('pa_patentes.rol', $rol)
        ->where('pa_patentes.pago_id', $pago_id)
        ->where('pa_pagos.situacion', 1)
        ->orderBy('pa_patentes.ano', 'asc')
        ->orderBy('pa_patentes.sem', 'asc')
        ->get();
    }

    public function obtenerPagosNoCentralizados(): Collection
    {
        return DB::table('pa_patentes')
        ->join('pa_pagos', function ($join) {
            $join->on('pa_patentes.ano', '=', 'pa_pagos.ano')
            ->on('pa_patentes.sem', '=', 'pa_pagos.sem')
            ->on('pa_patentes.rol', '=', 'pa_pagos.rol');
        })
        ->join('web_pago', 'pa_patentes.pago_id', '=', 'web_pago.pago_id')
        ->where('pa_pagos.situacion', 1)
        ->where('pa_pagos.estado_transferencia', '<>', 'PE')
        ->orderBy('pa_patentes.pago_id', 'asc')
        ->limit(20)
        ->get();
    }

    public function actualizarEmail(int $rut, string $dv, int $rol, string $email): bool
    {
        return DB::table('pa_patentes')
        ->where('rol', $rol)
        ->where('rut', $rut)
        ->where('dv', $dv)
        ->where('pago_id', '<>', 0)
        ->update(['correo_contacto' => $email]);
    }

    public function actualizarMontos(int $anio, int $sem, int $rol, int $interes, int $multa, int $total): bool
    {
        return DB::table('pa_pagos')
        ->where('ano', $anio)
        ->where('sem', $sem)
        ->where('rol', $rol)
        ->update(['interes' => $interes, 'multa' => $multa]);
    }

    public function pagarPatente(int $anio, int $sem, int $rol, string $pago_id, int $fecha_pago): bool
    {
        return DB::table('pa_patentes')
        ->where('ano', $anio)
        ->where('sem', $sem)
        ->where('rol', $rol)
        ->update(['pago_id' => $pago_id]);
    }

    public function guardarComprobantePdf(int $anio, int $sem, int $rol, string $pago_id, string $pdf): bool
    {
        return DB::table('pa_patentes')
        ->where('ano', $anio)
        ->where('sem', $sem)
        ->where('rol', $rol)
        ->where('pago_id', $pago_id)
        ->update(['doc_descar' => $pdf]);
    }

    public function centralizarPatente(int $anio, int $sem, int $rol, string $pago_id): bool
    {
        return DB::table('maestro_permisos')
        ->where('ano_cargo', $anio)
        ->where('placa_veh', $sem)
        ->where('tipo_cargo', $rol)
        ->where('id_pago', $pago_id)
        ->update(['estado_transferencia' => 'PE']);
    }
}