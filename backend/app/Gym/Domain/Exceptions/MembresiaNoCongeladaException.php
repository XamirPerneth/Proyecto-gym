<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class MembresiaNoCongeladaException extends DominioException
{
    public static function paraReactivar(): self
    {
        return new self('Solo se puede reactivar una membresía que se encuentre CONGELADA.');
    }
}
