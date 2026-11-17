<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\ValidarCheckIn;
use App\Gym\Infrastructure\Http\Requests\CheckInRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class CheckInController extends Controller
{
    public function __invoke(CheckInRequest $peticion, ValidarCheckIn $validarCheckIn): JsonResponse
    {
        $resultado = $validarCheckIn->ejecutar($peticion->string('documento')->toString());

        return response()->json(['data' => $resultado->aArreglo()]);
    }
}
