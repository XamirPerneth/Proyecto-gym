<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Controllers;

use App\Gym\Application\UseCases\ListarUltimosAccesos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccesoController extends ApiController
{
    private const LIMITE_POR_DEFECTO = 10;

    public function index(Request $peticion, ListarUltimosAccesos $listarAccesos): JsonResponse
    {
        $limite = (int) $peticion->query('limite', self::LIMITE_POR_DEFECTO);

        return $this->coleccion($listarAccesos->ejecutar($limite));
    }
}
