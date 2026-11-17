<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

final class SocioResumenDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $documento,
        public readonly string $nombre,
        public readonly ?MembresiaDTO $membresia,
    ) {
    }

    public function aArreglo(): array
    {
        return [
            'id' => $this->id,
            'documento' => $this->documento,
            'nombre' => $this->nombre,
            'membresia' => $this->membresia?->aArreglo(),
        ];
    }
}
