<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\SesionIniciadaDTO;
use App\Gym\Application\DTOs\UsuarioAutenticadoDTO;
use App\Gym\Domain\Exceptions\CredencialesInvalidasException;
use App\Gym\Domain\Ports\EmisorTokenPort;
use App\Gym\Domain\Ports\HasherContrasenaPort;
use App\Gym\Domain\Ports\UsuarioLecturaPort;

final class AutenticarUsuarioUseCase
{
    public function __construct(
        private readonly UsuarioLecturaPort $usuarios,
        private readonly HasherContrasenaPort $hasher,
        private readonly EmisorTokenPort $tokens,
    ) {
    }

    /**
     * @throws CredencialesInvalidasException
     */
    public function ejecutar(string $email, string $contrasenaPlana): SesionIniciadaDTO
    {
        $usuario = $this->usuarios->buscarPorEmail(trim(strtolower($email)));

        if ($usuario === null || !$this->hasher->verificar($contrasenaPlana, $usuario->hashContrasena())) {
            throw CredencialesInvalidasException::porDefecto();
        }

        $token = $this->tokens->emitirPara($usuario);

        return new SesionIniciadaDTO(
            $token,
            UsuarioAutenticadoDTO::desde($usuario),
        );
    }
}
