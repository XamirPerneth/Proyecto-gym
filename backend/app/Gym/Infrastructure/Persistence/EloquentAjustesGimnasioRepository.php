<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence;

use App\Gym\Domain\Entities\AjustesGimnasio;
use App\Gym\Domain\Ports\AjustesGimnasioPort;
use App\Gym\Domain\ValueObjects\PorcentajeDescuento;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\AjustesGimnasioModel;
use DateTimeImmutable;
use Throwable;

final class EloquentAjustesGimnasioRepository implements AjustesGimnasioPort
{
    public function obtener(): AjustesGimnasio
    {
        try {
            $fila = AjustesGimnasioModel::query()->first();
        } catch (Throwable) {
            $fila = null;
        }

        if ($fila === null) {
            return new AjustesGimnasio(
                'Gym Control',
                PorcentajeDescuento::de(10),
                PorcentajeDescuento::de(20),
                null,
            );
        }

        return $this->aDominio($fila);
    }

    public function guardar(AjustesGimnasio $ajustes): AjustesGimnasio
    {
        $fila = AjustesGimnasioModel::query()->first() ?? new AjustesGimnasioModel();

        $fila->fill([
            'nombre_gimnasio' => $ajustes->nombre(),
            'descuento_trimestral' => $ajustes->descuentoTrimestral()->valor(),
            'descuento_anual' => $ajustes->descuentoAnual()->valor(),
            'hora_simulada' => $ajustes->horaSimulada()?->format('Y-m-d H:i:s'),
        ]);
        $fila->save();

        return $this->aDominio($fila);
    }

    private function aDominio(AjustesGimnasioModel $fila): AjustesGimnasio
    {
        $horaSimulada = $fila->hora_simulada
            ? new DateTimeImmutable((string) $fila->hora_simulada)
            : null;

        return new AjustesGimnasio(
            $fila->nombre_gimnasio,
            PorcentajeDescuento::de((int) $fila->descuento_trimestral),
            PorcentajeDescuento::de((int) $fila->descuento_anual),
            $horaSimulada,
        );
    }
}
