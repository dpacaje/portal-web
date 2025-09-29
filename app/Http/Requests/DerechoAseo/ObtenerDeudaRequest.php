<?php

namespace App\Http\Requests\DerechoAseo;

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
            'rol' => 'required|numeric|digits_between:1,8',
            'roldv' => 'required|numeric|digits_between:1,8',
            'email' => 'required|email:rfc,dns',
        ];
    }

    public function messages(): array
    {
        return [
            'rol.required' => 'El ROL (Manzana) es requerido.',
            'rol.numeric' => 'El ROL (Manzana) debe ser numérico.',
            'rol.digits_between' => 'El ROL (Manzana) no es válido.',
            'roldv.required' => 'El ROL (Predio) es requerido.',
            'roldv.numeric' => 'El ROL (Predio) debe ser numérico.',
            'roldv.digits_between' => 'El ROL (Predio) no es válido.',
            'email.required' => 'El CORREO es requerido.',
            'email.email' => 'El CORREO debe cumplir el formato requerido.',
        ];
    }
}
