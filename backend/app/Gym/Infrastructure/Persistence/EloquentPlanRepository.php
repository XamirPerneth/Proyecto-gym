<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence;

use App\Gym\Domain\Entities\Plan;
use App\Gym\Domain\Ports\PlanEscrituraPort;
use App\Gym\Domain\Ports\PlanLecturaPort;
use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\RangoHorario;
use App\Gym\Infrastructure\Persistence\Eloquent\MapeadorDominio;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\PlanModel;

final class EloquentPlanRepository implements PlanLecturaPort, PlanEscrituraPort
{
    public function buscarPorId(int $id): ?Plan
    {
        $modelo = PlanModel::query()->find($id);

        return $modelo ? MapeadorDominio::aPlan($modelo) : null;
    }

    public function listar(): array
    {
        return PlanModel::query()
            ->orderBy('precio_base_mensual')
            ->get()
            ->map(fn (PlanModel $modelo): Plan => MapeadorDominio::aPlan($modelo))
            ->all();
    }

    public function actualizarConfiguracion(int $id, RangoHorario $rango, Dinero $precioBase): Plan
    {
        $modelo = PlanModel::query()->findOrFail($id);
        $modelo->update([
            'hora_inicio' => $rango->inicio(),
            'hora_fin' => $rango->fin(),
            'precio_base_mensual' => $precioBase->monto(),
        ]);

        return MapeadorDominio::aPlan($modelo->fresh());
    }
}
