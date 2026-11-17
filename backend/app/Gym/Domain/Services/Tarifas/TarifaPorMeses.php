<?php

declare(strict_types=1);

namespace App\Gym\Domain\Services\Tarifas;

use App\Gym\Domain\Ports\CalculadoraTarifaPort;
use App\Gym\Domain\ValueObjects\Dinero;

/** Base común: precio = precio mensual × meses, con un descuento porcentual fijo. */
abstract class TarifaPorMeses implements CalculadoraTarifaPort
{
    abstract protected function meses(): int;

    abstract protected function descuentoPorcentual(): int;

    public function calcularPrecio(Dinero $precioBaseMensual): Dinero
    {
        return $precioBaseMensual
            ->multiplicarPor($this->meses())
            ->conDescuento($this->descuentoPorcentual());
    }
}
