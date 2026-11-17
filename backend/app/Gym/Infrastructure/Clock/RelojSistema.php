<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Clock;

use App\Gym\Domain\Ports\AjustesGimnasioPort;
use App\Gym\Domain\Ports\RelojPort;
use DateTimeImmutable;
use DateTimeZone;

final class RelojSistema implements RelojPort
{
    public function __construct(
        private readonly string $zonaHoraria,
        private readonly ?string $horaSimuladaEnv = null,
        private readonly ?AjustesGimnasioPort $ajustes = null,
    ) {
    }

    public function ahora(): DateTimeImmutable
    {
        $zona = new DateTimeZone($this->zonaHoraria);

        $desdeAjustes = $this->ajustes?->obtener()->horaSimulada();
        if ($desdeAjustes !== null) {
            return new DateTimeImmutable($desdeAjustes->format('Y-m-d H:i:s'), $zona);
        }

        return new DateTimeImmutable($this->horaSimuladaEnv ?: 'now', $zona);
    }
}
