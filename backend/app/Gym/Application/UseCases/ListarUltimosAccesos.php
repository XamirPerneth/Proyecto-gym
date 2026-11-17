<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Domain\Ports\ConsultaAccesosPort;
use App\Gym\Domain\ReadModels\AccesoRegistrado;

final class ListarUltimosAccesos
{
    public function __construct(
        private readonly ConsultaAccesosPort $accesos,
    ) {
    }

    /**
     * @return array<int, array{id: int, socio: string, documento: string, plan: ?string, fechaHora: string, permitido: bool, motivo: ?string}>
     */
    public function ejecutar(int $limite = 10): array
    {
        $limiteSeguro = max(1, min(50, $limite));

        return array_map(
            static fn (AccesoRegistrado $acceso): array => [
                'id' => $acceso->id,
                'socio' => $acceso->socio,
                'documento' => $acceso->documento,
                'plan' => $acceso->plan ?? 'Sin plan',
                'fechaHora' => $acceso->fechaHora->format('Y-m-d H:i:s'),
                'permitido' => $acceso->permitido,
                'motivo' => $acceso->motivo,
            ],
            $this->accesos->listarUltimos($limiteSeguro),
        );
    }
}
