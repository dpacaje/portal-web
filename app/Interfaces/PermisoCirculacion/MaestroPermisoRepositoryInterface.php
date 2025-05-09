<?php

namespace App\Interfaces\PermisoCirculacion;

use Illuminate\Support\Collection;

interface MaestroPermisoRepositoryInterface
{
    // === READ ===
    public function obtenerDeudaAnterior(int $rut, string $dv, string $placa): Collection;
    public function obtenerDeudaActual(int $rut, string $dv, string $placa): Collection;
    public function obtenerPermisosPagados(int $rut, string $dv, string $placa): Collection;
    public function obtenerPermisosPagadosPorPagoid(string $placa, string $pago_id): Collection;
    public function obtenerPagosNoCentralizados(): Collection;
    // === UPDATE ===
    public function actualizarMontos(int $anio, string $placa, int $tipo_cargo, int $interes, int $multa, int $total): bool;
    public function pagarPermiso(int $anio, string $placa, int $tipo_cargo, string $pago_id, int $fecha_pago): bool;
    public function pagarPermisoDif(int $anio, string $placa, int $tipo_cargo, int $estado, string $descripcion): bool;
    public function centralizarPermiso(int $anio, string $placa, int $tipo_cargo, string $pago_id): bool;
}