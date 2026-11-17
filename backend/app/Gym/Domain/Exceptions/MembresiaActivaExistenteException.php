<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class MembresiaActivaExistenteException extends DominioException
{
    public static function paraSocio(string $nombreSocio): self
    {
        return new self("{$nombreSocio} ya tiene una membresía vigente; no puede adquirir otra hasta que finalice.");
    }
}
