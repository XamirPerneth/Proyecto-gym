<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

final class PlanDTO
{
    /**
     * @param array<int, array{tipo: string, duracionDias: int, precio: int}> $tarifas
     */
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly string $horaInicio,
        public readonly string $horaFin,
        public readonly array $tarifas,
    ) {
    }

    public function aArreglo(): array
    {
        return get_object_vars($this);
    }
}
