<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\ListarSocios;
use App\Gym\Application\UseCases\RegistrarSocio;
use App\Gym\Infrastructure\Http\Requests\RegistrarSocioRequest;
use Illuminate\Http\JsonResponse;

final class SocioController extends ApiController
{
    public function index(ListarSocios $listarSocios): JsonResponse
    {
        return $this->coleccion($listarSocios->ejecutar());
    }

    public function store(RegistrarSocioRequest $peticion, RegistrarSocio $registrarSocio): JsonResponse
    {
        $socio = $registrarSocio->ejecutar(
            $peticion->string('documento')->toString(),
            $peticion->string('nombre')->toString(),
        );

        return $this->creado($socio);
    }
}
