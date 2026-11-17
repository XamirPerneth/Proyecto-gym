<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\ResumenDashboardDTO;
use App\Gym\Domain\Ports\AjustesGimnasioPort;
use App\Gym\Domain\Ports\ConsultaEstadisticasPort;
use App\Gym\Domain\Ports\RelojPort;

final class ObtenerResumenDashboard
{
    private const FRANJAS_MOSTRADAS = [6, 8, 10, 12, 14, 16, 18, 20, 22];

    public function __construct(
        private readonly AjustesGimnasioPort $ajustes,
        private readonly ConsultaEstadisticasPort $estadisticas,
        private readonly RelojPort $reloj,
    ) {
    }

    public function ejecutar(): ResumenDashboardDTO
    {
        $ahora = $this->reloj->ahora();
        $ajustes = $this->ajustes->obtener();
        $resumen = $this->estadisticas->obtenerResumen($ahora);

        $franjas = [];
        foreach (self::FRANJAS_MOSTRADAS as $hora) {
            $conteo = ($resumen->accesosPermitidosPorHora[$hora] ?? 0)
                + ($resumen->accesosPermitidosPorHora[$hora + 1] ?? 0);

            $franjas[] = [
                'franja' => sprintf('%02d:00', $hora),
                'accesos' => $conteo,
            ];
        }

        return new ResumenDashboardDTO(
            $ajustes->nombre(),
            $resumen->sociosActivos,
            $resumen->accesosPermitidosHoy + $resumen->accesosDenegadosHoy,
            $resumen->accesosPermitidosHoy,
            $resumen->accesosDenegadosHoy,
            $resumen->membresiasCongeladas,
            $resumen->ingresosEstimados,
            $franjas,
        );
    }
}
