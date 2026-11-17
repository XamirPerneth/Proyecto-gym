<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\AjustesGimnasio;

interface AjustesGimnasioPort
{
    public function obtener(): AjustesGimnasio;

    public function guardar(AjustesGimnasio $ajustes): AjustesGimnasio;
}
