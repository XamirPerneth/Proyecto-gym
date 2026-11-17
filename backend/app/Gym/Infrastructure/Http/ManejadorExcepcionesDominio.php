<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http;

use App\Gym\Domain\Exceptions\AutenticacionException;
use App\Gym\Domain\Exceptions\DominioException;
use App\Gym\Domain\Exceptions\RecursoNoEncontradoException;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Traduce excepciones de dominio a respuestas HTTP: 401 auth, 404 inexistentes, 422 reglas de negocio. */
final class ManejadorExcepcionesDominio
{
    public static function comoCallback(): Closure
    {
        return static function (DominioException $excepcion, Request $peticion): ?JsonResponse {
            if (!$peticion->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => $excepcion->getMessage(),
                'error' => class_basename($excepcion),
            ], self::codigoHttp($excepcion));
        };
    }

    private static function codigoHttp(DominioException $excepcion): int
    {
        if ($excepcion instanceof AutenticacionException) {
            return Response::HTTP_UNAUTHORIZED;
        }

        if ($excepcion instanceof RecursoNoEncontradoException) {
            return Response::HTTP_NOT_FOUND;
        }

        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
