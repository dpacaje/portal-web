<?php

namespace App\Interfaces\PermisoCirculacion;

use Illuminate\Support\Collection;

interface MultaTransitoRepositoryInterface
{
    // === READ ===
    public function obtenerDeuda(string $placa): Collection;
    public function obtenerMultaPagadaPorId(string $placa, string $pago_id): Collection;
    // === UPDATE ===
    public function pagarMulta(int $multa_id, string $pago_id): bool;
}