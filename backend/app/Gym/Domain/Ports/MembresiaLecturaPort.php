<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\Membresia;

interface MembresiaLecturaPort
{
    public function buscarPorId(int $id): ?Membresia;

    /** Devuelve la membresía más reciente del socio, sin importar su estado. */
    public function buscarUltimaPorSocio(int $socioId): ?Membresia;
}
