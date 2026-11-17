<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\UsuarioAutenticadoDTO;
use App\Gym\Domain\Exceptions\TokenInvalidoException;
use App\Gym\Domain\Ports\EmisorTokenPort;

final class ObtenerUsuarioAutenticado
{
    public function __construct(
        private readonly EmisorTokenPort $tokens,
    ) {
    }

    /**
     * @throws TokenInvalidoException
     */
    public function ejecutar(?string $tokenClaro): UsuarioAutenticadoDTO
    {
        if ($tokenClaro === null || trim($tokenClaro) === '') {
            throw TokenInvalidoException::noAutenticado();
        }

        $usuario = $this->tokens->buscarUsuarioPorToken($tokenClaro)
            ?? throw TokenInvalidoException::noAutenticado();

        return UsuarioAutenticadoDTO::desde($usuario);
    }
}
