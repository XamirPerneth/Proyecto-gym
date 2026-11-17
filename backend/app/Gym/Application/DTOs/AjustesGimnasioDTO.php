<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

use App\Gym\Domain\Entities\AjustesGimnasio;
use App\Gym\Domain\Entities\Plan;

final class AjustesGimnasioDTO
{
    /**
     * @param array<int, array{id: int, nombre: string, horaInicio: string, horaFin: string, precioBaseMensual: int}> $planes
     */
    public function __construct(
        public readonly string $nombreGimnasio,
        public readonly int $descuentoTrimestral,
        public readonly int $descuentoAnual,
        public readonly ?string $horaSimulada,
        public readonly array $planes,
    ) {
    }

    /**
     * @param Plan[] $planes
     */
    public static function desde(AjustesGimnasio $ajustes, array $planes): self
    {
        return new self(
            $ajustes->nombre(),
            $ajustes->descuentoTrimestral()->valor(),
            $ajustes->descuentoAnual()->valor(),
            $ajustes->horaSimulada()?->format('Y-m-d H:i'),
            array_map(
                static fn (Plan $plan): array => [
                    'id' => $plan->id(),
                    'nombre' => $plan->nombre(),
                    'horaInicio' => $plan->rangoHorario()->inicio(),
                    'horaFin' => $plan->rangoHorario()->fin(),
                    'precioBaseMensual' => $plan->precioBaseMensual()->monto(),
                ],
                $planes,
            ),
        );
    }

    public function aArreglo(): array
    {
        return get_object_vars($this);
    }
}
