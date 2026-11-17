<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class CredencialesInvalidasException extends AutenticacionException
{
    public static function porDefecto(): self
    {
        return new self('Correo o contraseña incorrectos. Verifica tus credenciales e inténtalo de nuevo.');
    }
}
