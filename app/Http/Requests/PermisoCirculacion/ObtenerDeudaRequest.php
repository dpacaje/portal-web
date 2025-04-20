<?php

namespace App\Http\Requests\PermisoCirculacion;

use Illuminate\Foundation\Http\FormRequest;

class ObtenerDeudaRequest extends FormRequest
{
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
        ];
    }
}
