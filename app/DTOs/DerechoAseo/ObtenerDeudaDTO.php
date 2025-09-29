<?php

namespace App\DTOs\DerechoAseo;

class ObtenerDeudaDTO
{
    public function __construct(
        public int $rol,
        public int $roldv,
        public string $email,
    ) {}

    public static function fromArray(Array $data): self
    {
        return new self(
            rol: (int) $data['rol'],
            roldv: (int) $data['roldv'],
            email: $data['email'],
        );
    }
}