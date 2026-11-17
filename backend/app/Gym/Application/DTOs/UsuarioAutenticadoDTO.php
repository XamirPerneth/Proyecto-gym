<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

use App\Gym\Domain\Entities\Usuario;

final class UsuarioAutenticadoDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly string $email,
        public readonly string $rol,
    ) {
    }

    public static function desde(Usuario $usuario): self
    {
        return new self(
            $usuario->id(),
            $usuario->nombre(),
            $usuario->email(),
            $usuario->rol(),
        );
    }

    public function aArreglo(): array
    {
        return get_object_vars($this);
    }
}
