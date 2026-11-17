<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

final class ResumenDashboardDTO
{
    /**
     * @param array<int, array{franja: string, accesos: int}> $ocupacionPorFranja
     */
    public function __construct(
        public readonly string $nombreGimnasio,
        public readonly int $sociosActivos,
        public readonly int $accesosHoy,
        public readonly int $accesosPermitidosHoy,
        public readonly int $accesosDenegadosHoy,
        public readonly int $membresiasCongeladas,
        public readonly int $ingresosEstimados,
        public readonly array $ocupacionPorFranja,
    ) {
    }

    public function aArreglo(): array
    {
        return get_object_vars($this);
    }
}
