<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\Socio;

interface SocioLecturaPort
{
    public function buscarPorId(int $id): ?Socio;

    public function buscarPorDocumento(string $documento): ?Socio;
}
