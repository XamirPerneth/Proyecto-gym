<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\Socio;

interface SocioEscrituraPort
{
    public function registrar(string $documento, string $nombre): Socio;
}
