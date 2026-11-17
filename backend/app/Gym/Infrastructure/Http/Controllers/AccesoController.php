<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\ListarUltimosAccesos;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccesoController extends Controller
{
    public function index(Request $peticion, ListarUltimosAccesos $listarAccesos): JsonResponse
    {
        $limite = (int) $peticion->query('limite', 10);

        return response()->json([
            'data' => $listarAccesos->ejecutar($limite),
        ]);
    }
}
