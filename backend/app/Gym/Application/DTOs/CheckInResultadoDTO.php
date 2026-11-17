<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

final class CheckInResultadoDTO
{
    public function __construct(
        public readonly bool $accesoPermitido,
        public readonly string $socio,
        public readonly string $documento,
        public readonly string $plan,
        public readonly string $horarioPermitido,
        public readonly string $fechaVencimiento,
        public readonly int $diasRestantes,
        public readonly string $horaIngreso,
    ) {
    }

    public function aArreglo(): array
    {
        return get_object_vars($this);
    }
}
