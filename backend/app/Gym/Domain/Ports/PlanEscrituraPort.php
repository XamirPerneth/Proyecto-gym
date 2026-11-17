<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\Plan;
use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\RangoHorario;

interface PlanEscrituraPort
{
    public function actualizarConfiguracion(int $id, RangoHorario $rango, Dinero $precioBase): Plan;
}
