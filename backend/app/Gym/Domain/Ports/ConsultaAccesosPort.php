<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\ReadModels\AccesoRegistrado;

interface ConsultaAccesosPort
{
    /** @return AccesoRegistrado[] */
    public function listarUltimos(int $limite = 10): array;
}
