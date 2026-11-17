<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\ObtenerResumenDashboard;
use Illuminate\Http\JsonResponse;

final class DashboardController extends ApiController
{
    public function __invoke(ObtenerResumenDashboard $obtenerResumen): JsonResponse
    {
        return $this->ok($obtenerResumen->ejecutar());
    }
}
