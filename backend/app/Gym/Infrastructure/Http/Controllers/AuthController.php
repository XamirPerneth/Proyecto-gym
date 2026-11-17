<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\AutenticarUsuarioUseCase;
use App\Gym\Application\UseCases\CerrarSesion;
use App\Gym\Application\UseCases\ObtenerUsuarioAutenticado;
use App\Gym\Infrastructure\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController extends Controller
{
    public function login(LoginRequest $peticion, AutenticarUsuarioUseCase $autenticar): JsonResponse
    {
        $sesion = $autenticar->ejecutar(
            $peticion->string('email')->toString(),
            $peticion->string('password')->toString(),
        );

        return response()->json(['data' => $sesion->aArreglo()]);
    }

    public function me(Request $peticion, ObtenerUsuarioAutenticado $obtenerUsuario): JsonResponse
    {
        $usuario = $obtenerUsuario->ejecutar($peticion->bearerToken());

        return response()->json(['data' => $usuario->aArreglo()]);
    }

    public function logout(Request $peticion, CerrarSesion $cerrarSesion): JsonResponse
    {
        $cerrarSesion->ejecutar((string) $peticion->bearerToken());

        return response()->json(['data' => ['ok' => true]]);
    }
}
