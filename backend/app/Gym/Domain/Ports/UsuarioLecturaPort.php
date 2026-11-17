<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\Usuario;

interface UsuarioLecturaPort
{
    public function buscarPorEmail(string $email): ?Usuario;
}
