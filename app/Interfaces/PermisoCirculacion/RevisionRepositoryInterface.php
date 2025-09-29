<?php

namespace App\Interfaces\PermisoCirculacion;

use stdClass;

interface RevisionRepositoryInterface
{
    // === READ ===
    public function obtenerRevisionValida(int $ano_min, int $ano_max, string $placa, string $fecha_actual): ?stdClass;
    public function obtenerUltimaRevision(string $placa): ?stdClass;
    // === UPDATE ===
    public function registrarRevision(string $codigo, string $placa, string $cod_resultado, string $comuna, string $direccion, string $fecha_inicio, string $fecha_termino, string $id_crt, string $mensaje, string $nro_certificado, string $region, string $responsable): bool;
    public function eliminarRevision(string $placa): bool;
}