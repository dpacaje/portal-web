<?php

namespace App\DTOs\PatenteMunicipal;

class ObtenerDeudaDTO
{
    public function __construct(
        public int $rutn,
        public string $rutdv,
        public int $rol,
        public string $email,
    ) {}

    public static function fromArray(Array $data): self
    {
        $arr_rut = separar_rut($data['rut']);

        return new self(
            rutn: (int) $arr_rut[0],
            rutdv: $arr_rut[1],
            rol: (int) $data['rol'],
            email: $data['email'],
        );
    }
}