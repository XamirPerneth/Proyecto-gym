<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\DTOs\AsignarMembresiaComando;
use App\Gym\Application\UseCases\AsignarMembresia;
use App\Gym\Application\UseCases\CongelarMembresia;
use App\Gym\Application\UseCases\ReactivarMembresia;
use App\Gym\Infrastructure\Http\Requests\AsignarMembresiaRequest;
use App\Gym\Infrastructure\Http\Requests\CongelarMembresiaRequest;
use Illuminate\Http\JsonResponse;

final class MembresiaController extends ApiController
{
    public function store(AsignarMembresiaRequest $peticion, AsignarMembresia $asignarMembresia): JsonResponse
    {
        $membresia = $asignarMembresia->ejecutar(new AsignarMembresiaComando(
            $peticion->integer('socio_id'),
            $peticion->integer('plan_id'),
            $peticion->string('tipo_tarifa')->toString(),
        ));

        return $this->creado($membresia);
    }

    public function congelar(
        int $id,
        CongelarMembresiaRequest $peticion,
        CongelarMembresia $congelarMembresia,
    ): JsonResponse {
        return $this->ok($congelarMembresia->ejecutar($id, $peticion->integer('dias')));
    }

    public function reactivar(int $id, ReactivarMembresia $reactivarMembresia): JsonResponse
    {
        return $this->ok($reactivarMembresia->ejecutar($id));
    }
}
