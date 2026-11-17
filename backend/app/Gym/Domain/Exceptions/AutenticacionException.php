<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

/** Base de los fallos de identidad: la capa HTTP los traduce a 401. */
abstract class AutenticacionException extends DominioException
{
}
