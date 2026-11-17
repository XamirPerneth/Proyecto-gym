<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence;

use App\Gym\Domain\Entities\Membresia;
use App\Gym\Domain\Ports\MembresiaEscrituraPort;
use App\Gym\Domain\Ports\MembresiaLecturaPort;
use App\Gym\Infrastructure\Persistence\Eloquent\MapeadorDominio;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\MembresiaModel;

final class EloquentMembresiaRepository implements MembresiaLecturaPort, MembresiaEscrituraPort
{
    private const FORMATO_FECHA = 'Y-m-d';

    public function buscarPorId(int $id): ?Membresia
    {
        $modelo = MembresiaModel::query()->with('plan')->find($id);

        return $modelo ? MapeadorDominio::aMembresia($modelo) : null;
    }

    public function buscarUltimaPorSocio(int $socioId): ?Membresia
    {
        $modelo = MembresiaModel::query()
            ->with('plan')
            ->where('socio_id', $socioId)
            ->orderByDesc('id')
            ->first();

        return $modelo ? MapeadorDominio::aMembresia($modelo) : null;
    }

    public function guardar(Membresia $membresia): Membresia
    {
        $modelo = MembresiaModel::query()->updateOrCreate(
            ['id' => $membresia->id()],
            [
                'socio_id' => $membresia->socioId(),
                'plan_id' => $membresia->plan()->id(),
                'tipo_tarifa' => $membresia->tipoTarifa()->value,
                'estado' => $membresia->estadoPersistido()->value,
                'fecha_inicio' => $membresia->fechaInicio()->format(self::FORMATO_FECHA),
                'fecha_fin' => $membresia->fechaFin()->format(self::FORMATO_FECHA),
                'precio_final' => $membresia->precioFinal()->monto(),
                'fecha_congelamiento' => $membresia->fechaCongelamiento()?->format(self::FORMATO_FECHA),
                'dias_congelamiento' => $membresia->diasCongelamiento(),
                'dias_congelados_acumulados' => $membresia->diasCongeladosAcumulados(),
            ],
        );

        return $membresia->conId($modelo->id);
    }
}
