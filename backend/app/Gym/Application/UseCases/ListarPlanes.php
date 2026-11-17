<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\PlanDTO;
use App\Gym\Domain\Entities\Plan;
use App\Gym\Domain\Ports\CalculadoraTarifaPort;
use App\Gym\Domain\Ports\PlanLecturaPort;
use App\Gym\Domain\Services\SelectorTarifa;

final class ListarPlanes
{
    public function __construct(
        private readonly PlanLecturaPort $planes,
        private readonly SelectorTarifa $tarifas,
    ) {
    }

    /** @return PlanDTO[] */
    public function ejecutar(): array
    {
        return array_map(fn (Plan $plan): PlanDTO => $this->aDto($plan), $this->planes->listar());
    }

    private function aDto(Plan $plan): PlanDTO
    {
        return new PlanDTO(
            $plan->id(),
            $plan->nombre(),
            $plan->rangoHorario()->inicio(),
            $plan->rangoHorario()->fin(),
            array_map(
                static fn (CalculadoraTarifaPort $tarifa): array => [
                    'tipo' => $tarifa->tipo()->value,
                    'duracionDias' => $tarifa->duracionEnDias(),
                    'precio' => $tarifa->calcularPrecio($plan->precioBaseMensual())->monto(),
                ],
                $this->tarifas->todas(),
            ),
        );
    }
}
