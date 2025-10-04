<?php

namespace App\Services\PermisoCirculacion;

use App\Interfaces\PermisoCirculacion\WebPagoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebPagoService
{
    public function __construct(
        protected readonly WebPagoRepositoryInterface $_webPagoRepository,
    ) {}

    public function validarUltimosIntentosPagos(string $placa)
    {
        $fecha_limite = calcular_fecha();
        $intentos_pagos = $this->_webPagoRepository->obtenerUltimosIntentosPagos($placa, $fecha_limite);

        if ($intentos_pagos->isEmpty()) {
            return false;
        }

        foreach ($intentos_pagos as $row) {
            //
        }
    }
}