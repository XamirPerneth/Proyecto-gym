<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\RegistroAcceso;

interface RegistroAccesoEscrituraPort
{
    public function registrar(RegistroAcceso $registro): void;
}
