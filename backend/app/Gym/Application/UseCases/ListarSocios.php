<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\MembresiaDTO;
use App\Gym\Application\DTOs\SocioResumenDTO;
use App\Gym\Domain\Ports\ConsultaSociosPort;
use App\Gym\Domain\Ports\RelojPort;
use App\Gym\Domain\ReadModels\SocioConMembresia;

final class ListarSocios
{
    public function __construct(
        private readonly ConsultaSociosPort $consulta,
        private readonly RelojPort $reloj,
    ) {
    }

    /** @return SocioResumenDTO[] */
    public function ejecutar(): array
    {
        $ahora = $this->reloj->ahora();

        return array_map(
            static fn (SocioConMembresia $item): SocioResumenDTO => new SocioResumenDTO(
                $item->socio->id(),
                $item->socio->documento(),
                $item->socio->nombre(),
                $item->ultimaMembresia ? MembresiaDTO::desde($item->ultimaMembresia, $ahora) : null,
            ),
            $this->consulta->listarConUltimaMembresia(),
        );
    }
}
