<?php

namespace App\Http\Controllers\DerechoAseo;

use App\DTOs\DerechoAseo\ObtenerDeudaDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\DerechoAseo\ObtenerDeudaRequest;
use App\Services\DerechoAseo\CuotaAseoService;

class CuotaAseoController extends Controller
{
    public function __construct(
        private readonly CuotaAseoService $_cuotaAseoService,
    ) {}

    public function index()
    {
        return view('DerechoAseo.index');
    }

    public function deuda(ObtenerDeudaRequest $request)
    {
        try {
            $dto = ObtenerDeudaDTO::fromArray($request->validated());

            $propietario = $this->_cuotaAseoService->obtenerPropietario($dto);

            if (!$propietario) {
                return view('DerechoAseo.sin_deuda');
            }

            $deuda = $this->_cuotaAseoService->obtenerDeuda($dto);

            $data['rol'] = $dto->rol;
            $data['roldv'] = $dto->roldv;
            $data['email'] = $dto->email;
            $data['propietario'] = $propietario;
            $data['deuda'] = $deuda;

            return view('DerechoAseo.deuda')->with($data);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}