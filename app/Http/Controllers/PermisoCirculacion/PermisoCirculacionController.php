<?php

namespace App\Http\Controllers\PermisoCirculacion;

use App\DTOs\PermisoCirculacion\ObtenerDeudaDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermisoCirculacion\ObtenerDeudaRequest;
use App\Services\PermisoCirculacion\MaestroPermisoService;

class PermisoCirculacionController extends Controller
{
    private MaestroPermisoService $_maestroPermisoService;

    public function __construct(
        MaestroPermisoService $maestroPermisoService,
    )
    {
        $this->_maestroPermisoService = $maestroPermisoService;
    }

    public function index()
    {
        return view('PermisoCirculacion.index');
    }

    public function deuda(ObtenerDeudaRequest $request)
    {
        $data = ObtenerDeudaDTO::formRequest($request->validated());
        $data = $this->_maestroPermisoService->obtenerDeuda($data);
        dd($data);
    }
}