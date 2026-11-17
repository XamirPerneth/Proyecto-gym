<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence;

use App\Gym\Domain\Ports\ConsultaEstadisticasPort;
use App\Gym\Domain\ReadModels\EstadisticasGimnasio;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\MembresiaModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\RegistroAccesoModel;
use DateTimeImmutable;

final class EloquentEstadisticasRepository implements ConsultaEstadisticasPort
{
    public function obtenerResumen(DateTimeImmutable $ahora): EstadisticasGimnasio
    {
        $fechaHoy = $ahora->format('Y-m-d');

        $sociosActivos = MembresiaModel::query()
            ->where('estado', 'ACTIVA')
            ->whereDate('fecha_fin', '>=', $fechaHoy)
            ->count();

        $membresiasCongeladas = MembresiaModel::query()
            ->where('estado', 'CONGELADA')
            ->count();

        $ingresosEstimados = (int) MembresiaModel::query()
            ->whereIn('estado', ['ACTIVA', 'CONGELADA'])
            ->whereDate('fecha_fin', '>=', $fechaHoy)
            ->sum('precio_final');

        $accesosHoy = RegistroAccesoModel::query()
            ->whereDate('fecha_hora', $fechaHoy)
            ->get();

        $permitidosHoy = 0;
        $denegadosHoy = 0;
        $porHora = [];

        foreach ($accesosHoy as $acceso) {
            if ($acceso->permitido) {
                $permitidosHoy++;
                $hora = (int) (new DateTimeImmutable((string) $acceso->fecha_hora))->format('G');
                $porHora[$hora] = ($porHora[$hora] ?? 0) + 1;
            } else {
                $denegadosHoy++;
            }
        }

        return new EstadisticasGimnasio(
            $sociosActivos,
            $permitidosHoy,
            $denegadosHoy,
            $membresiasCongeladas,
            $ingresosEstimados,
            $porHora,
        );
    }
}
