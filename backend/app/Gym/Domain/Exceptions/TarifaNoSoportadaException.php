<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class TarifaNoSoportadaException extends DominioException
{
    public static function para(string $tipo): self
    {
        return new self("No existe una tarifa configurada para el tipo '{$tipo}'.");
    }
}
