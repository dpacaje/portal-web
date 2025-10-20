<?php

namespace App\Interfaces\PermisoCirculacion;

use Illuminate\Support\Collection;
use stdClass;

interface WebPagoRepositoryInterface
{
    // === READ ===
    public function obtenerUltimoIntentoPago(string $placa, int $fecha_limite): ?stdclass;
    public function obtenerUltimoPagoId(string $pago_id): ?stdclass;
    public function obtenerPorPagoId(string $pago_id): ?stdclass;
    // === UPDATE ===
    public function crear(array $data): bool;
    public function agregarToken(string $pago_id, string $token, string $sistema_pagador, string $ecosistema, string $navegador, string $navegador_version): bool;
    public function pagar(string $pago_id, array $data);
    public function rechazar(string $pago_id, array $data);
}