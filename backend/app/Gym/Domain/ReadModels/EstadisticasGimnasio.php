<?php

declare(strict_types=1);

namespace App\Gym\Domain\ReadModels;

final class EstadisticasGimnasio
{
    /**
     * @param array<int, int> $accesosPermitidosPorHora hora (0-23) => cantidad de ingresos permitidos de hoy
     */
    public function __construct(
        public readonly int $sociosActivos,
        public readonly int $accesosPermitidosHoy,
        public readonly int $accesosDenegadosHoy,
        public readonly int $membresiasCongeladas,
        public readonly int $ingresosEstimados,
        public readonly array $accesosPermitidosPorHora,
    ) {
    }
}
