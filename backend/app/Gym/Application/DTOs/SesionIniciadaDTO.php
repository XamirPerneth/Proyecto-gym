<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

final class SesionIniciadaDTO
{
    public function __construct(
        public readonly string $token,
        public readonly UsuarioAutenticadoDTO $usuario,
    ) {
    }

    public function aArreglo(): array
    {
        return [
            'token' => $this->token,
            'usuario' => $this->usuario->aArreglo(),
        ];
    }
}
