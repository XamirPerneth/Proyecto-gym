<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class MembresiaNoEncontradaException extends RecursoNoEncontradoException
{
    public static function conId(int $id): self
    {
        return new self("No existe una membresía con el identificador {$id}.");
    }
}
