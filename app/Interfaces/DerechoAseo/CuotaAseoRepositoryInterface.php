<?php

namespace App\Interfaces\DerechoAseo;

use Illuminate\Support\Collection;
use stdClass;

interface CuotaAseoRepositoryInterface
{
    // === READ ===
    public function obtenerDatosPropietarioCuotaAseo(int $rol, int $roldv): ?stdClass;
    public function obtenerDeudaActual(int $rol, int $roldv): Collection;
    public function obtenerCuotaAseoPagadas(int $rol, int $roldv): Collection;
    public function obtenerCuotaAseoPagadasPorPagoid(int $rol, int $roldv, string $pago_id): Collection;
    public function obtenerPagosNoCentralizados(): Collection;
    // === UPDATE ===
    public function actualizarEmail(int $rol, int $roldv, string $email): bool;
    public function actualizarMontos(int $anio, int $rol, int $roldv, int $cuota, int $interes, int $multa): bool;
    public function pagarCuotaAseo(int $anio, int $rol, int $roldv, int $cuota, string $pago_id, int $fecha_pago): bool;
    public function guardarComprobantePdf(int $anio, int $rol, int $roldv, int $cuota, string $pago_id, string $pdf): bool;
    public function centralizarCuotaAseo(int $anio, int $rol, int $roldv, int $cuota, string $pago_id): bool;
}