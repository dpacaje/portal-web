<?php

namespace App\Interfaces\PermisoCirculacion;

use stdClass;

interface SeguroRepositoryInterface
{
    // === READ ===
    public function obtenerSeguroValido(int $ano, string $placa, int $fecha): ?stdClass;
    public function obtenerUltimoSeguro(string $placa): ?stdClass;
    // === UPDATE ===
    public function registrarSeguro(string $placa, string $code, string $nombre_compania, string $nro_poliza, string $fecha_inicio, string $fecha_termino, string $responsable): bool;
    public function eliminarSeguro(string $placa): bool;
}