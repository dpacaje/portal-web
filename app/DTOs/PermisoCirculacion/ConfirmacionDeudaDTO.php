<?php

namespace App\DTOs\PermisoCirculacion;

class ConfirmacionDeudaDTO
{
    public function __construct(
        public int $rutn,
        public string $rutdv,
        public string $placa,
        public string $email,
        public int $monto_permiso,
        public int $monto_multa,
        public array|null $permiso_anterior,
        public string|null $permiso_actual,
    ) {}

    public static function fromArray(Array $data): self
    {
        $arr_rut = separar_rut($data['rut']);

        return new self(
            rutn: (int) $arr_rut[0],
            rutdv: $arr_rut[1],
            placa: strtoupper($data['placa']),
            email: $data['email'],
            monto_permiso: $data['montopermiso'],
            monto_multa: $data['montomulta'],
            permiso_anterior: $data['pago_ant'] ?? null,
            permiso_actual: $data['check-pago'] ?? null,
        );
    }
}