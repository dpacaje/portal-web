<?php

namespace App\Http\Controllers\PatenteMunicipal;

use App\DTOs\PatenteMunicipal\ObtenerDeudaDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PatenteMunicipal\ObtenerDeudaRequest;
use App\Services\PatenteMunicipal\PatenteService;

class PatenteMunicipalController extends Controller
{
    public function __construct(
        private readonly PatenteService $_patenteService,
    ) {}

    public function index()
    {
        return view('PatenteMunicipal.index');
    }

    public function deuda(ObtenerDeudaRequest $request)
    {
        try {
            $dto = ObtenerDeudaDTO::fromArray($request->validated());

            $propietario = $this->_patenteService->obtenerPropietario($dto);

            if (!$propietario) {
                return view('PatenteMunicipal.sin_deuda');
            }

            $deuda = $this->_patenteService->obtenerDeuda($dto);

            $data['rut'] = $dto->rutn . '-' . $dto->rutdv;
            $data['rol'] = $dto->rol;
            $data['email'] = $dto->email;
            $data['propietario'] = $propietario;
            $data['deuda'] = $deuda;

            return view('PatenteMunicipal.deuda')->with($data);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}