<?php

namespace App\Repositories\ParametrosGenerales;

use App\Interfaces\ParametrosGenerales\ParametrosGeneralesRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class ParametrosGeneralesRepository implements ParametrosGeneralesRepositoryInterface
{
    public function getUtm(int $anio, int $mes): ?stdClass
    {
        return DB::table('parametros_generales.utm')
        ->where('utm_ano', $anio)
        ->where('utm_mes', $mes)
        ->first(['utm_utm']);
    }

    public function getIpc(int $anio, int $mes, int $anio_tabla): ?stdClass
    {
        return DB::table('parametros_generales.maestro_multas')
        ->where('ano', $anio)
        ->where('mes', $mes)
        ->where('ano_tabla', $anio_tabla)
        ->get()
        ->first();
    }
}