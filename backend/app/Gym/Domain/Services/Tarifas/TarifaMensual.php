<?php

declare(strict_types=1);

namespace App\Gym\Domain\Services\Tarifas;

use App\Gym\Domain\ValueObjects\TipoTarifa;

final class TarifaMensual extends TarifaPorMeses
{
    private const MESES = 1;
    private const DESCUENTO_PORCENTUAL = 0;
    private const DURACION_EN_DIAS = 30;

    public function tipo(): TipoTarifa
    {
        return TipoTarifa::MENSUAL;
    }

    public function duracionEnDias(): int
    {
        return self::DURACION_EN_DIAS;
    }

    protected function meses(): int
    {
        return self::MESES;
    }

    protected function descuentoPorcentual(): int
    {
        return self::DESCUENTO_PORCENTUAL;
    }
}
