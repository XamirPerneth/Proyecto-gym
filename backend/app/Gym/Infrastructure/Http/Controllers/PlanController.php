<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\ListarPlanes;
use Illuminate\Http\JsonResponse;

final class PlanController extends ApiController
{
    public function index(ListarPlanes $listarPlanes): JsonResponse
    {
        return $this->coleccion($listarPlanes->ejecutar());
    }
}
