<?php

namespace App\Http\Controllers\PermisoCirculacion;

use App\DTOs\PermisoCirculacion\ConfirmacionDeudaDTO;
use App\DTOs\PermisoCirculacion\ObtenerDeudaDTO;
use App\Enums\TipoPortal;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermisoCirculacion\ConfirmacionDeudaRequest;
use App\Http\Requests\PermisoCirculacion\ObtenerDeudaRequest;
use App\Services\PermisoCirculacion\MaestroPermisoService;
use App\Services\PermisoCirculacion\MultaTransitoService;
use App\Services\PermisoCirculacion\RevisionService;
use App\Services\PermisoCirculacion\SeguroService;
use App\Services\PermisoCirculacion\WebPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PermisoCirculacionController extends Controller
{
    public function __construct(
        protected readonly MaestroPermisoService $_maestroPermisoService,
        protected readonly MultaTransitoService $_multaTransitoService,
        protected readonly SeguroService $_seguroService,
        protected readonly RevisionService $_revisionService,
        protected readonly WebPagoService $_webPagoService,
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

    public function confirmacion(ConfirmacionDeudaRequest $request)
    {
        try {
            $dto = ConfirmacionDeudaDTO::fromArray($request->validated());

            $ultimo_intento_pago = $this->_webPagoService->validarUltimosIntentosPagos($dto->placa);

            if ($ultimo_intento_pago) {
                return view('PermisoCirculacion.index');
            }

            $permisos_corectos = $this->_maestroPermisoService->validarPermisosPrePago($dto->monto_permiso, $dto->permiso_anterior, $dto->permiso_actual);

            if (!$permisos_corectos) {
                return view('PermisoCirculacion.index');
            }

            $monto_multa_recalculada = $this->_multaTransitoService->obtenerDeudaMonto($dto->placa);

            if ($dto->monto_multa != $monto_multa_recalculada) {
                return view('PermisoCirculacion.index');
            }

            $detalle_permiso = $this->_maestroPermisoService->prepararDetallePrePago($dto);
            $detalle_multa = $this->_multaTransitoService->prepararDetallePrePago($dto->placa);

            $data['rut'] = $dto->rutn . '-' . $dto->rutdv;
            $data['placa'] = $dto->placa;
            $data['email'] = $dto->email;
            $data['monto_permiso'] = $dto->monto_permiso;
            $data['monto_multa'] = $dto->monto_multa;
            $data['monto_total'] = (int) $dto->monto_permiso + $dto->monto_multa;
            $data['permiso_anterior'] = $dto->permiso_anterior;
            $data['permiso_actual'] = $dto->permiso_actual;

            $datos_calculados = [
                'placa' => $dto->placa,
                'monto_permiso' => $dto->monto_permiso,
                'monto_multa' => $dto->monto_multa,
                'monto_total' => $dto->monto_permiso + $dto->monto_multa,
                'detalle_permiso' => $detalle_permiso,
                'detalle_multa' => $detalle_multa,
                'tipoportal' => TipoPortal::PERMISOCIRCULACION,
                'fecha_expiracion' => now()->addMinutes(30)->timestamp
            ];

            $datos_calculados_encriptados = Crypt::encrypt($datos_calculados);

            $data['hdndata'] = $datos_calculados_encriptados;

            return view('PermisoCirculacion.confirmacion')->with($data);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}