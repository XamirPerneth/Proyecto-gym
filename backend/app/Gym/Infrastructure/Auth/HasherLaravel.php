<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Auth;

use App\Gym\Domain\Ports\HasherContrasenaPort;
use Illuminate\Support\Facades\Hash;

final class HasherLaravel implements HasherContrasenaPort
{
    public function verificar(string $plana, string $hash): bool
    {
        return Hash::check($plana, $hash);
    }
}
