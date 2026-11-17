<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\MembresiaDTO;
use App\Gym\Domain\Exceptions\MembresiaNoEncontradaException;
use App\Gym\Domain\Ports\MembresiaEscrituraPort;
use App\Gym\Domain\Ports\MembresiaLecturaPort;
use App\Gym\Domain\Ports\RelojPort;

final class ReactivarMembresia
{
    public function __construct(
        private readonly MembresiaLecturaPort $lectura,
        private readonly MembresiaEscrituraPort $escritura,
        private readonly RelojPort $reloj,
    ) {
    }

    public function ejecutar(int $membresiaId): MembresiaDTO
    {
        $membresia = $this->lectura->buscarPorId($membresiaId)
            ?? throw MembresiaNoEncontradaException::conId($membresiaId);

        $membresia->reactivar();

        return MembresiaDTO::desde($this->escritura->guardar($membresia), $this->reloj->ahora());
    }
}
