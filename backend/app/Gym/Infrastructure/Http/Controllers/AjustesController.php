<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\DTOs\ActualizarAjustesComando;
use App\Gym\Application\UseCases\ActualizarAjustes;
use App\Gym\Application\UseCases\ObtenerAjustes;
use App\Gym\Infrastructure\Http\Requests\ActualizarAjustesRequest;
use Illuminate\Http\JsonResponse;

final class AjustesController extends ApiController
{
    public function show(ObtenerAjustes $obtenerAjustes): JsonResponse
    {
        return $this->ok($obtenerAjustes->ejecutar());
    }

    public function update(ActualizarAjustesRequest $peticion, ActualizarAjustes $actualizarAjustes): JsonResponse
    {
        $ajustes = $actualizarAjustes->ejecutar(new ActualizarAjustesComando(
            $peticion->string('nombre_gimnasio')->toString(),
            $peticion->integer('descuento_trimestral'),
            $peticion->integer('descuento_anual'),
            $peticion->filled('hora_simulada') ? $peticion->string('hora_simulada')->toString() : null,
            (array) $peticion->input('planes', []),
        ));

        return $this->ok($ajustes);
    }
}
