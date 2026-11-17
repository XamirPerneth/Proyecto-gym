<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class TokenInvalidoException extends AutenticacionException
{
    public static function noAutenticado(): self
    {
        return new self('No autenticado: inicia sesión para continuar.');
    }
}
