<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\Membresia;

interface MembresiaEscrituraPort
{
    /** Persiste la membresía y devuelve la instancia con su identificador asignado. */
    public function guardar(Membresia $membresia): Membresia;
}
