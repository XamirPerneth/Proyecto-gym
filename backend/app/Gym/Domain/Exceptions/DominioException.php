<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

use DomainException;

/**
 * Raíz de todas las violaciones de reglas de negocio del gimnasio.
 * La capa HTTP la traduce a una respuesta con un mensaje claro.
 */
abstract class DominioException extends DomainException
{
}
