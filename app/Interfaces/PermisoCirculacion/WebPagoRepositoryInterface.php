<?php

namespace App\Interfaces\PermisoCirculacion;

use Illuminate\Support\Collection;
use stdClass;

interface WebPagoRepositoryInterface
{
    // === READ ===
    public function obtenerUltimosIntentosPagos(string $placa, int $fecha_limite): Collection;
    public function obtenerUltimoPagoId(string $pago_id): stdclass;
    // === UPDATE ===
    public function pagarPermisoDif(int $anio, string $placa, int $tipo_cargo, int $estado, string $descripcion): bool;
    public function centralizarPermiso(int $anio, string $placa, int $tipo_cargo, string $pago_id): bool;
}