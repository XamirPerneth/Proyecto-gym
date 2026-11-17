<?php

declare(strict_types=1);

namespace App\Gym\Domain\ReadModels;

use App\Gym\Domain\Entities\Membresia;
use App\Gym\Domain\Entities\Socio;

final class SocioConMembresia
{
    public function __construct(
        public readonly Socio $socio,
        public readonly ?Membresia $ultimaMembresia,
    ) {
    }
}
