<?php

namespace App\Http\Controllers\PermisoCirculacion;

use App\Enums\EstadoPagoEnum;
use App\Enums\TipoPortal;
use App\Http\Controllers\Controller;
use App\Services\PermisoCirculacion\MaestroPermisoService;
use App\Services\PermisoCirculacion\MultaTransitoService;
use App\Services\PermisoCirculacion\RevisionService;
use App\Services\PermisoCirculacion\SeguroService;
use App\Services\PermisoCirculacion\WebPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;

class WebPagoController extends Controller
{
    public function __construct(
        protected readonly MaestroPermisoService $_maestroPermisoService,
        protected readonly MultaTransitoService $_multaTransitoService,
        protected readonly SeguroService $_seguroService,
        protected readonly RevisionService $_revisionService,
        protected readonly WebPagoService $_webPagoService,
    ) {}

    public function iniciarPago(Request $request)
    {
        $request->validate([
            'hdndata' => 'required|string',
        ]);

        try {
            $datos = Crypt::decrypt($request->input('hdndata'));

            $pago_id = $this->_webPagoService->crear($datos['tipoportal']->value, $datos['placa'], $datos['detalle_permiso'], $datos['monto_permiso'], $datos['detalle_multa'], $datos['monto_multa']);

            if ($pago_id === false) {
                throw new \Exception('No se pudo generar nuevo pago_id.');
            }

            $url_tbk = $this->_webPagoService->iniciarTransaccion($pago_id, $datos['monto_total']);

            $this->_webPagoService->actualizarToken($pago_id, $url_tbk);

            return redirect($url_tbk);
        } catch (\Throwable $e) {
            dd($e);
        }
    }

    public function validarPago(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $session_id = $request->input('session_id');

            $pago_id = $this->_webPagoService->validarTransaccion($session_id);

            return redirect(URL::temporarySignedRoute('permisocirculacion.resultadopago', now()->addMinutes(20), ['id' => $pago_id]));
        } catch (\Throwable $e) {
            dd($e);
        }
    }

    public function resultadoPago(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('permisocirculacion.index');
        }

        $data = $this->_webPagoService->buscarPorId($id);

        if (!$data) {
            return redirect()->route('permisocirculacion.index');
        }

        if ($data->estado == EstadoPagoEnum::PAGADO) {
            return view('PermisoCirculacion.aprobado')->with('data', $data);
        } else if ($data->estado == EstadoPagoEnum::RECHAZADO) {
            return view('PermisoCirculacion.rechazado')->with('data', $data);
        } else {
            return redirect()->route('permisocirculacion.index');
        }
    }
}