<?php

namespace App\Http\Requests\PermisoCirculacion;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmacionDeudaRequest extends FormRequest
{
    protected $redirectRoute = 'permisocirculacion.index';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rut' => 'required|regex:/^[0-9]+-[0-9Kk]$/',
            'placa' => 'required|alpha_num|size:6',
            'email' => 'required|email:rfc,dns',
            'montomulta' => 'required|integer|min:0',
            'montopermiso' => 'required|integer|min:100',
            'pago_ant' => 'required_without:check-pago|nullable|array',
            'pago_ant.*' => 'required|string|regex:/^\d+_[a-zA-Z0-9]{6}_[012]_\d{4}$/',
            'check-pago' => 'required_without:pago_ant|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'rut.required' => 'El RUT es requerido.',
            'rut.regex' => 'El RUT debe cumplir el formato requerido.',
            'placa.required' => 'La PLACA es requerida.',
            'placa.alpha_num' => 'La PLACA debe cumplir el formato requerido.',
            'placa.size' => 'La PLACA debe tener 6 caracteres.',
            'email.required' => 'El CORREO es requerido.',
            'email.email' => 'El CORREO debe cumplir el formato requerido.',
            'montomulta.required' => 'El MONTO MULTA es requerido.',
            'montomulta.integer' => 'El MONTO MULTA debe cumplir el formato requerido.',
            'montomulta.min' => 'El MONTO MULTA debe tener un monto efectivo.',
            'montopermiso.required' => 'El MONTO PERMISO es requerido.',
            'montopermiso.integer' => 'El MONTO PERMISO debe cumplir el formato requerido.',
            'montopermiso.min' => 'El MONTO PERMISO debe tener un monto efectivo.',
        ];
    }
}
