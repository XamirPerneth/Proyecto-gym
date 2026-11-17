<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\ValidarCheckIn;
use App\Gym\Infrastructure\Http\Requests\CheckInRequest;
use Illuminate\Http\JsonResponse;

final class CheckInController extends ApiController
{
    public function __invoke(CheckInRequest $peticion, ValidarCheckIn $validarCheckIn): JsonResponse
    {
        return $this->ok($validarCheckIn->ejecutar($peticion->string('documento')->toString()));
    }
}
