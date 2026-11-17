<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\ObtenerResumenDashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class DashboardController extends Controller
{
    public function __invoke(ObtenerResumenDashboard $obtenerResumen): JsonResponse
    {
        return response()->json([
            'data' => $obtenerResumen->ejecutar()->aArreglo(),
        ]);
    }
}
