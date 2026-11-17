<?php

declare(strict_types=1);

namespace App\Gym\Domain\ReadModels;

use DateTimeImmutable;

final class AccesoRegistrado
{
    public function __construct(
        public readonly int $id,
        public readonly string $socio,
        public readonly string $documento,
        public readonly ?string $plan,
        public readonly DateTimeImmutable $fechaHora,
        public readonly bool $permitido,
        public readonly ?string $motivo,
    ) {
    }
}
