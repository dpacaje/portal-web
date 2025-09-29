<?php

namespace App\Http\Requests\PatenteMunicipal;

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
            'rol' => 'required|numeric|digits_between:4,7',
            'email' => 'required|email:rfc,dns',
        ];
    }

    public function messages(): array
    {
        return [
            'rut.required' => 'El RUT es requerido.',
            'rut.regex' => 'El RUT debe cumplir el formato requerido.',
            'rol.required' => 'El ROL es requerido.',
            'rol.numeric' => 'El ROL debe ser numérico.',
            'rol.digits_between' => 'El ROL no es válido.',
            'email.required' => 'El CORREO es requerido.',
            'email.email' => 'El CORREO debe cumplir el formato requerido.',
        ];
    }
}
