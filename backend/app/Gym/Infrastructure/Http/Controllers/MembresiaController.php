<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\DTOs\AsignarMembresiaComando;
use App\Gym\Application\UseCases\AsignarMembresia;
use App\Gym\Application\UseCases\CongelarMembresia;
use App\Gym\Application\UseCases\ReactivarMembresia;
use App\Gym\Infrastructure\Http\Requests\AsignarMembresiaRequest;
use App\Gym\Infrastructure\Http\Requests\CongelarMembresiaRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class MembresiaController extends Controller
{
    public function store(AsignarMembresiaRequest $peticion, AsignarMembresia $asignarMembresia): JsonResponse
    {
        $membresia = $asignarMembresia->ejecutar(new AsignarMembresiaComando(
            $peticion->integer('socio_id'),
            $peticion->integer('plan_id'),
            $peticion->string('tipo_tarifa')->toString(),
        ));

        return response()->json(['data' => $membresia->aArreglo()], Response::HTTP_CREATED);
    }

    public function congelar(
        int $id,
        CongelarMembresiaRequest $peticion,
        CongelarMembresia $congelarMembresia,
    ): JsonResponse {
        $membresia = $congelarMembresia->ejecutar($id, $peticion->integer('dias'));

        return response()->json(['data' => $membresia->aArreglo()]);
    }

    public function reactivar(int $id, ReactivarMembresia $reactivarMembresia): JsonResponse
    {
        return response()->json(['data' => $reactivarMembresia->ejecutar($id)->aArreglo()]);
    }
}
