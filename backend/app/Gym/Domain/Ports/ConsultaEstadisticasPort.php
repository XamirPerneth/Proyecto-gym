<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\ReadModels\EstadisticasGimnasio;
use DateTimeImmutable;

interface ConsultaEstadisticasPort
{
    public function obtenerResumen(DateTimeImmutable $ahora): EstadisticasGimnasio;
}
