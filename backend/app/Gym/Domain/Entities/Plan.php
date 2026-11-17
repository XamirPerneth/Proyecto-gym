<?php

declare(strict_types=1);

namespace App\Gym\Domain\Entities;

use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\RangoHorario;

final class Plan
{
    public function __construct(
        private readonly int $id,
        private readonly string $nombre,
        private readonly RangoHorario $rangoHorario,
        private readonly Dinero $precioBaseMensual,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function rangoHorario(): RangoHorario
    {
        return $this->rangoHorario;
    }

    public function precioBaseMensual(): Dinero
    {
        return $this->precioBaseMensual;
    }
}
