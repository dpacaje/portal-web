<?php

namespace App\Http\Controllers\PermisoCirculacion;

use App\DTOs\PermisoCirculacion\ObtenerDeudaDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermisoCirculacion\ObtenerDeudaRequest;
use App\Services\PermisoCirculacion\MaestroPermisoService;
use App\Services\PermisoCirculacion\MultaTransitoService;
use App\Services\PermisoCirculacion\RevisionService;
use App\Services\PermisoCirculacion\SeguroService;

class PermisoCirculacionController extends Controller
{
    public function __construct(
        protected readonly MaestroPermisoService $_maestroPermisoService,
        protected readonly MultaTransitoService $_multaTransitoService,
        protected readonly SeguroService $_seguroService,
        protected readonly RevisionService $_revisionService,
    ) {}

    public function index()
    {
        return view('PermisoCirculacion.index');
    }

    public function deuda(ObtenerDeudaRequest $request)
    {
        try {
            $dto = ObtenerDeudaDTO::fromArray($request->validated());

            $propietario = $this->_maestroPermisoService->obtenerPropietario($dto);

            if (!$propietario) {
                return view('PermisoCirculacion.sin_deuda');
            }

            $permisos = $this->_maestroPermisoService->obtenerDeuda($dto);

            $monto_multa = $this->_multaTransitoService->obtenerDeudaMonto($dto->placa);

            $ultimo_permiso = $this->_maestroPermisoService->obtenerUltimoPermisoDeuda($permisos);

            $estado_seguro = true;//$this->_seguroService->verificarSeguro($ultimo_permiso->ano_cargo, $ultimo_permiso->placa_veh, $ultimo_permiso->tipo_cargo, $ultimo_permiso->id_tipo_vehiculo);

            $estado_revision = true;//$this->_revisionService->verificarRevision($ultimo_permiso->ano_cargo, $ultimo_permiso->placa_veh, $ultimo_permiso->tipo_cargo);

            // dd($permisos, $monto_multa, $estado_seguro, $estado_revision);

            $data['rut'] = $dto->rutn . '-' . $dto->rutdv;
            $data['placa'] = $dto->placa;
            $data['email'] = $dto->email;
            $data['propietario'] = $propietario;
            $data['permisos_anterior'] = $permisos['anterior'];
            $data['permisos_actual'] = $permisos['actual'];
            $data['monto_multa'] = $monto_multa;
            $data['seguro_soap'] = $estado_seguro;
            $data['revision_tecnica'] = $estado_revision;

            return view('PermisoCirculacion.deuda')->with($data);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}