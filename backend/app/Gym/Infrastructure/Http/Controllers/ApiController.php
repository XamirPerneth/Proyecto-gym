<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controlador base para el adaptador HTTP: centraliza el formato uniforme de respuestas JSON.
 */
abstract class ApiController extends Controller
{
    protected function ok(mixed $dato): JsonResponse
    {
        return response()->json(['data' => $this->serializar($dato)]);
    }

    protected function creado(mixed $dato): JsonResponse
    {
        return response()->json(['data' => $this->serializar($dato)], Response::HTTP_CREATED);
    }

    /** @param array<int, mixed> $elementos */
    protected function coleccion(array $elementos): JsonResponse
    {
        return response()->json([
            'data' => array_map(fn (mixed $item): mixed => $this->serializar($item), $elementos),
        ]);
    }

    private function serializar(mixed $dato): mixed
    {
        if (is_object($dato) && method_exists($dato, 'aArreglo')) {
            return $dato->aArreglo();
        }

        return $dato;
    }
}
