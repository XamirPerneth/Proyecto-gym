<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class SocioNoEncontradoException extends RecursoNoEncontradoException
{
    public static function conDocumento(string $documento): self
    {
        return new self("No existe un socio registrado con el documento {$documento}.");
    }

    public static function conId(int $id): self
    {
        return new self("No existe un socio con el identificador {$id}.");
    }
}
