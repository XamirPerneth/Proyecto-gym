<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

final class ActualizarAjustesComando
{
    /**
     * @param array<int, array{id: int, hora_inicio: string, hora_fin: string, precio_base_mensual: int}> $planes
     */
    public function __construct(
        public readonly string $nombreGimnasio,
        public readonly int $descuentoTrimestral,
        public readonly int $descuentoAnual,
        public readonly ?string $horaSimulada,
        public readonly array $planes,
    ) {
    }
}
