<?php

namespace App\DTOs\PermisoCirculacion;

class ObtenerDeudaDTO
{
    public function __construct(
        public string $rut,
        public string $placa,
        public string $email,
    ) {}

    public static function formRequest(array $data): self
    {
        return new self(
            rut: $data['rut'],
            placa: $data['placa'],
            email: $data['email'],
        );
    }
}