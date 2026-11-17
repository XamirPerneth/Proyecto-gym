<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence;

use App\Gym\Domain\Entities\RegistroAcceso;
use App\Gym\Domain\Ports\ConsultaAccesosPort;
use App\Gym\Domain\Ports\RegistroAccesoEscrituraPort;
use App\Gym\Domain\ReadModels\AccesoRegistrado;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\RegistroAccesoModel;
use DateTimeImmutable;

final class EloquentRegistroAccesoRepository implements RegistroAccesoEscrituraPort, ConsultaAccesosPort
{
    public function registrar(RegistroAcceso $registro): void
    {
        RegistroAccesoModel::query()->create([
            'socio_id' => $registro->socioId(),
            'membresia_id' => $registro->membresiaId(),
            'fecha_hora' => $registro->fechaHora()->format('Y-m-d H:i:s'),
            'permitido' => $registro->fuePermitido(),
            'motivo' => $registro->motivo(),
        ]);
    }

    public function listarUltimos(int $limite = 10): array
    {
        return RegistroAccesoModel::query()
            ->with(['socio', 'membresia.plan'])
            ->orderByDesc('fecha_hora')
            ->orderByDesc('id')
            ->limit($limite)
            ->get()
            ->map(static fn (RegistroAccesoModel $fila): AccesoRegistrado => new AccesoRegistrado(
                $fila->id,
                $fila->socio?->nombre ?? 'Socio desconocido',
                $fila->socio?->documento ?? '',
                $fila->membresia?->plan?->nombre,
                new DateTimeImmutable((string) $fila->fecha_hora),
                (bool) $fila->permitido,
                $fila->motivo,
            ))
            ->all();
    }
}
