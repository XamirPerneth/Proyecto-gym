<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\TipoTarifa;

/** Estrategia de precio y duración para un tipo de tarifa (OCP: se agregan planes sin tocar casos de uso). */
interface CalculadoraTarifaPort
{
    public function tipo(): TipoTarifa;

    public function duracionEnDias(): int;

    public function calcularPrecio(Dinero $precioBaseMensual): Dinero;
}
