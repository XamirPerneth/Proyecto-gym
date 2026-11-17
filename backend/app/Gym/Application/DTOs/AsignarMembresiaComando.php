<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

final class AsignarMembresiaComando
{
    public function __construct(
        public readonly int $socioId,
        public readonly int $planId,
        public readonly string $tipoTarifa,
    ) {
    }
}
