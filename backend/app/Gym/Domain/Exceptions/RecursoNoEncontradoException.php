<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

/** Se lanza cuando un socio, plan o membresía solicitado no existe. */
abstract class RecursoNoEncontradoException extends DominioException
{
}
