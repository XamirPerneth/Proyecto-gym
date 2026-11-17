<?php

declare(strict_types=1);

namespace App\Gym\Domain\Entities;

use App\Gym\Domain\Exceptions\ValorInvalidoException;
use App\Gym\Domain\ValueObjects\PorcentajeDescuento;
use DateTimeImmutable;

final class AjustesGimnasio
{
    public function __construct(
        private readonly string $nombre,
        private readonly PorcentajeDescuento $descuentoTrimestral,
        private readonly PorcentajeDescuento $descuentoAnual,
        private readonly ?DateTimeImmutable $horaSimulada = null,
    ) {
        if (trim($nombre) === '') {
            throw ValorInvalidoException::nombreGimnasioVacio();
        }
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function descuentoTrimestral(): PorcentajeDescuento
    {
        return $this->descuentoTrimestral;
    }

    public function descuentoAnual(): PorcentajeDescuento
    {
        return $this->descuentoAnual;
    }

    public function horaSimulada(): ?DateTimeImmutable
    {
        return $this->horaSimulada;
    }
}
