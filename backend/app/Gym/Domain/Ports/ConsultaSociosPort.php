<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\ReadModels\SocioConMembresia;

interface ConsultaSociosPort
{
    /** @return SocioConMembresia[] */
    public function listarConUltimaMembresia(): array;
}
