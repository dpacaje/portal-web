<?php

namespace App\Interfaces\ParametrosGenerales;

use Illuminate\Support\Collection;
use stdClass;

interface ParametrosGeneralesRepositoryInterface
{
    public function getUtm(int $anio, int $mes): ?stdClass;
    public function getIpc(int $anio, int $mes, int $anio_tabla): ?stdClass;
}