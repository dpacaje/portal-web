<?php

namespace App\Repositories\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\MultaTransitoRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MultaTransitoRepository implements MultaTransitoRepositoryInterface
{
    public function obtenerDeuda(string $placa): Collection
    {
        return DB::table('pc_pago_2da_cuota_multa')
        ->select(['placa', 'multa_id', 'fecha', 'nivel', 'valor', 'arancel', 'tag'])
        ->where('placa', $placa)
        ->where('estado', 0)
        ->get();
    }

    public function obtenerMultaPagadaPorId(string $placa, string $pago_id): Collection
    {
        return DB::table('pc_pago_2da_cuota_multa as p2')
        ->select(['p2.*', 'left(wp.fecha_pago, 8) as fecha_pago', 'wp.monto_multa'])
        ->join('web_pago as wp', 'wp.pago_id', '=', 'p2.pago_id')
        ->where('p2.placa', $placa)
        ->where('p2.estado', 1)
        ->where('p2.pago_id', $pago_id)
        ->get();
    }

    public function pagarMulta(int $multa_id, string $pago_id): bool
    {
        return DB::table('pc_pago_2da_cuota_multa')
        ->where('multa_id', $multa_id)
        ->update(['estado' => 1, 'pago_id' => $pago_id]);
    }
}