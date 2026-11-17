<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class MembresiaNoCongelableException extends DominioException
{
    public static function porEstado(string $estado): self
    {
        return new self("Solo se puede congelar una membresía ACTIVA; la membresía está {$estado}.");
    }
}
