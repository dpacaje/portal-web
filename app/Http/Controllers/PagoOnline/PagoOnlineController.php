<?php

namespace App\Http\Controllers\PagoOnline;

use App\DTOs\PermisoCirculacion\ConfirmacionDeudaDTO;
use App\DTOs\PermisoCirculacion\ObtenerDeudaDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermisoCirculacion\ConfirmacionDeudaRequest;
use App\Http\Requests\PermisoCirculacion\ObtenerDeudaRequest;
use App\Services\PagoOnline\PagoOnlineService;
use App\Services\PermisoCirculacion\MaestroPermisoService;
use App\Services\PermisoCirculacion\MultaTransitoService;
use App\Services\PermisoCirculacion\RevisionService;
use App\Services\PermisoCirculacion\SeguroService;
use App\Services\PermisoCirculacion\WebPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PagoOnlineController extends Controller
{
    public function __construct(
        protected readonly MaestroPermisoService $_maestroPermisoService,
        protected readonly PagoOnlineService $_pagoOnlineService,
        protected readonly WebPagoService $_webPagoService,
    ) {}

    public function crear(Request $request)
    {
        try {
            $request->validate([
                'hdndata' => 'required|string',
            ]);

            $datos_descifrados = Crypt::decrypt($request->input('hdndata'));
            // dd($datos_descifrados);

            $datos = $this->_pagoOnlineService->gestionarInicioPago($datos_descifrados);
            dd($datos);

            // Mostrar Vista
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}