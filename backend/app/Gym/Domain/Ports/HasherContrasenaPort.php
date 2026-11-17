<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

interface HasherContrasenaPort
{
    public function verificar(string $plana, string $hash): bool;
}
