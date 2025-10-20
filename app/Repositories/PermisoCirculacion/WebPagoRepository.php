<?php

namespace App\Repositories\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\WebPagoRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class WebPagoRepository implements WebPagoRepositoryInterface
{
    public function obtenerUltimoIntentoPago(string $placa, int $fecha_limite): ?stdclass
    {
        return DB::table('pc_web_pago')
        ->select('pago_id', 'token')
        ->where('estado', 'Creado')
        ->where('placa_gral', $placa)
        ->whereRaw('LEFT(fecha_stamp, 8) >= ?', [$fecha_limite])
        ->whereRaw('LENGTH(token) > ?', [130])
        ->orderBy('pago_id', 'desc')
        ->first();
    }

    public function obtenerUltimoPagoId(string $pago_id): ?stdclass
    {
        return DB::table('pc_web_pago')
        ->select('pago_id')
        ->where('pago_id', 'LIKE', $pago_id . '%')
        ->orderBy('pago_id', 'desc')
        ->lockForUpdate()
        ->first();
    }

    public function obtenerPorPagoId(string $pago_id): ?stdclass
    {
        return DB::table('pc_web_pago')
        ->where('pago_id', $pago_id)
        ->first();
    }

    public function crear(array $data): bool
    {
        return DB::table('pc_web_pago')
        ->insert($data);
    }

    public function agregarToken(string $pago_id, string $token, string $sistema_pagador, string $ecosistema, string $navegador, string $navegador_version): bool
    {
        return DB::table('pc_web_pago')
        ->where('pago_id', $pago_id)
        ->update(['plataforma_pagador' => $sistema_pagador, 'ecosistema' => $ecosistema, 'navegador' => $navegador, 'version_nav' => $navegador_version, 'token' => $token]);
    }

    public function pagar(string $pago_id, array $data)
    {
        return DB::table('pc_web_pago')
        ->where('pago_id', $pago_id)
        ->update($data);
    }

    public function rechazar(string $pago_id, array $data)
    {
        return DB::table('pc_web_pago')
        ->where('pago_id', $pago_id)
        ->update($data);
    }
}