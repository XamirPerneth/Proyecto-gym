<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Middleware;

use App\Gym\Application\UseCases\ObtenerUsuarioAutenticado;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AutenticarTokenGym
{
    public function __construct(
        private readonly ObtenerUsuarioAutenticado $obtenerUsuario,
    ) {
    }

    public function handle(Request $peticion, Closure $siguiente): Response
    {
        $usuario = $this->obtenerUsuario->ejecutar($peticion->bearerToken());
        $peticion->attributes->set('gym_usuario', $usuario);

        return $siguiente($peticion);
    }
}
