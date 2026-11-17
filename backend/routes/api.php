<?php

use App\Gym\Infrastructure\Http\Controllers\AccesoController;
use App\Gym\Infrastructure\Http\Controllers\AjustesController;
use App\Gym\Infrastructure\Http\Controllers\AuthController;
use App\Gym\Infrastructure\Http\Controllers\CheckInController;
use App\Gym\Infrastructure\Http\Controllers\DashboardController;
use App\Gym\Infrastructure\Http\Controllers\MembresiaController;
use App\Gym\Infrastructure\Http\Controllers\PlanController;
use App\Gym\Infrastructure\Http\Controllers\SocioController;
use App\Gym\Infrastructure\Http\Middleware\AutenticarTokenGym;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (/api/v1)
|--------------------------------------------------------------------------
| Las rutas solo delegan en controladores del adaptador HTTP de app/Gym.
*/

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(AutenticarTokenGym::class)->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('dashboard', DashboardController::class);
        Route::post('check-in', CheckInController::class);
        Route::get('accesos', [AccesoController::class, 'index']);

        Route::get('socios', [SocioController::class, 'index']);
        Route::post('socios', [SocioController::class, 'store']);
        Route::get('planes', [PlanController::class, 'index']);

        Route::post('membresias', [MembresiaController::class, 'store']);
        Route::post('membresias/{id}/congelar', [MembresiaController::class, 'congelar'])->whereNumber('id');
        Route::post('membresias/{id}/reactivar', [MembresiaController::class, 'reactivar'])->whereNumber('id');

        Route::get('ajustes', [AjustesController::class, 'show']);
        Route::put('ajustes', [AjustesController::class, 'update']);
    });
});
