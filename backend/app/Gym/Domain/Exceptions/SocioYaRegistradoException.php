<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class SocioYaRegistradoException extends DominioException
{
    public static function conDocumento(string $documento): self
    {
        return new self("Ya existe un socio registrado con el documento {$documento}.");
    }
}
