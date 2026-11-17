<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\Entities\Plan;

interface PlanLecturaPort
{
    public function buscarPorId(int $id): ?Plan;

    /** @return Plan[] */
    public function listar(): array;
}
