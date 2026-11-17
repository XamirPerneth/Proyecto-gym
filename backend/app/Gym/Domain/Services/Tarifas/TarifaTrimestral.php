<?php

declare(strict_types=1);

namespace App\Gym\Domain\Services\Tarifas;

use App\Gym\Domain\Ports\AjustesGimnasioPort;
use App\Gym\Domain\ValueObjects\TipoTarifa;

final class TarifaTrimestral extends TarifaPorMeses
{
    private const MESES = 3;
    private const DESCUENTO_POR_DEFECTO = 10;
    private const DURACION_EN_DIAS = 90;

    public function __construct(
        private readonly ?AjustesGimnasioPort $ajustes = null,
    ) {
    }

    public function tipo(): TipoTarifa
    {
        return TipoTarifa::TRIMESTRAL;
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
        return $this->ajustes?->obtener()->descuentoTrimestral()->valor() ?? self::DESCUENTO_POR_DEFECTO;
    }
}
