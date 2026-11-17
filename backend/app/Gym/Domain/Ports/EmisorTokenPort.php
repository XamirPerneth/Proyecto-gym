<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\Usuario;

interface EmisorTokenPort
{
    public function emitirPara(Usuario $usuario): string;

    public function buscarUsuarioPorToken(string $tokenClaro): ?Usuario;

    public function revocar(string $tokenClaro): void;
}
