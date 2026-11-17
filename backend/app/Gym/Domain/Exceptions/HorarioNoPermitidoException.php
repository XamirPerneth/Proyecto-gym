<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

use App\Gym\Domain\ValueObjects\RangoHorario;

final class HorarioNoPermitidoException extends DominioException
{
    public static function fueraDeRango(string $nombrePlan, RangoHorario $rango, string $horaActual): self
    {
        return new self(
            "Acceso denegado: el plan {$nombrePlan} solo permite el ingreso entre {$rango->descripcion()} "
            . "y son las {$horaActual}."
        );
    }
}
