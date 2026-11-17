<?php

declare(strict_types=1);

namespace App\Gym\Domain\Entities;

final class Socio
{
    public function __construct(
        private readonly int $id,
        private readonly string $documento,
        private readonly string $nombre,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function documento(): string
    {
        return $this->documento;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }
}
