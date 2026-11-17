<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class PlanNoEncontradoException extends RecursoNoEncontradoException
{
    public static function conId(int $id): self
    {
        return new self("No existe un plan con el identificador {$id}.");
    }
}
