<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class PeriodoCongelamientoInvalidoException extends DominioException
{
    public static function fueraDeLimites(int $dias, int $minimo, int $maximo): self
    {
        return new self(
            "El congelamiento debe ser de {$minimo} a {$maximo} días; se solicitaron {$dias}."
        );
    }
}
