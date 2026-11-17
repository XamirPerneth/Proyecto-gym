<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Domain\Ports\EmisorTokenPort;

final class CerrarSesion
{
    public function __construct(
        private readonly EmisorTokenPort $tokens,
    ) {
    }

    public function ejecutar(string $tokenClaro): void
    {
        if (trim($tokenClaro) !== '') {
            $this->tokens->revocar($tokenClaro);
        }
    }
}
