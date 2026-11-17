<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\DTOs\SocioResumenDTO;
use App\Gym\Application\UseCases\ListarSocios;
use App\Gym\Application\UseCases\RegistrarSocio;
use App\Gym\Infrastructure\Http\Requests\RegistrarSocioRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class SocioController extends Controller
{
    public function index(ListarSocios $listarSocios): JsonResponse
    {
        return response()->json([
            'data' => array_map(
                static fn (SocioResumenDTO $socio): array => $socio->aArreglo(),
                $listarSocios->ejecutar(),
            ),
        ]);
    }

    public function store(RegistrarSocioRequest $peticion, RegistrarSocio $registrarSocio): JsonResponse
    {
        $socio = $registrarSocio->ejecutar(
            $peticion->string('documento')->toString(),
            $peticion->string('nombre')->toString(),
        );

        return response()->json(['data' => $socio->aArreglo()], Response::HTTP_CREATED);
    }
}
