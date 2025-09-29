<?php

namespace App\Interfaces\PatenteMunicipal;

use Illuminate\Support\Collection;
use stdClass;

interface PatenteRepositoryInterface
{
    // === READ ===
    public function obtenerDatosPropietarioPatente(int $rut, string $dv, int $rol): ?stdClass;
    public function obtenerDeudaActual(int $rut, string $dv, int $rol): Collection;
    public function obtenerPatentesPagadas(int $rut, string $dv, int $rol): Collection;
    public function obtenerPatentesPagadasPorPagoid(int $rol, string $pago_id): Collection;
    public function obtenerPagosNoCentralizados(): Collection;
    // === UPDATE ===
    public function actualizarEmail(int $rut, string $dv, int $rol, string $email): bool;
    public function actualizarMontos(int $anio, int $sem, int $rol, int $interes, int $multa, int $total): bool;
    public function pagarPatente(int $anio, int $sem, int $rol, string $pago_id, int $fecha_pago): bool;
    public function guardarComprobantePdf(int $anio, int $sem, int $rol, string $pago_id, string $pdf): bool;
    public function centralizarPatente(int $anio, int $sem, int $rol, string $pago_id): bool;
}