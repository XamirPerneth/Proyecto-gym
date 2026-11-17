<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

use App\Gym\Domain\ValueObjects\EstadoMembresia;

final class MembresiaInactivaException extends DominioException
{
    public static function porEstado(EstadoMembresia $estado): self
    {
        return new self("Acceso denegado: la membresía se encuentra {$estado->etiqueta()}.");
    }

    public static function sinMembresia(): self
    {
        return new self('Acceso denegado: el socio no tiene ninguna membresía registrada.');
    }
}
