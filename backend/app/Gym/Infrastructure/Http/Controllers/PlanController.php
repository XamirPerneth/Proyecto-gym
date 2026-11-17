<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\DTOs\PlanDTO;
use App\Gym\Application\UseCases\ListarPlanes;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class PlanController extends Controller
{
    public function index(ListarPlanes $listarPlanes): JsonResponse
    {
        return response()->json([
            'data' => array_map(
                static fn (PlanDTO $plan): array => $plan->aArreglo(),
                $listarPlanes->ejecutar(),
            ),
        ]);
    }
}
