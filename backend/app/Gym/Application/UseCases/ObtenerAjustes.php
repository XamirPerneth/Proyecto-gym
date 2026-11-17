<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\AjustesGimnasioDTO;
use App\Gym\Domain\Ports\AjustesGimnasioPort;
use App\Gym\Domain\Ports\PlanLecturaPort;

final class ObtenerAjustes
{
    public function __construct(
        private readonly AjustesGimnasioPort $ajustes,
        private readonly PlanLecturaPort $planes,
    ) {
    }

    public function ejecutar(): AjustesGimnasioDTO
    {
        return AjustesGimnasioDTO::desde(
            $this->ajustes->obtener(),
            $this->planes->listar(),
        );
    }
}
