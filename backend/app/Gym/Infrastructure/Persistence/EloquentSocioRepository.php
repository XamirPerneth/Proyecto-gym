<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence;

use App\Gym\Domain\Entities\Socio;
use App\Gym\Domain\Ports\ConsultaSociosPort;
use App\Gym\Domain\Ports\SocioEscrituraPort;
use App\Gym\Domain\Ports\SocioLecturaPort;
use App\Gym\Domain\ReadModels\SocioConMembresia;
use App\Gym\Infrastructure\Persistence\Eloquent\MapeadorDominio;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\SocioModel;

final class EloquentSocioRepository implements SocioLecturaPort, SocioEscrituraPort, ConsultaSociosPort
{
    public function buscarPorId(int $id): ?Socio
    {
        $modelo = SocioModel::query()->find($id);

        return $modelo ? MapeadorDominio::aSocio($modelo) : null;
    }

    public function buscarPorDocumento(string $documento): ?Socio
    {
        $modelo = SocioModel::query()->where('documento', $documento)->first();

        return $modelo ? MapeadorDominio::aSocio($modelo) : null;
    }

    public function registrar(string $documento, string $nombre): Socio
    {
        $modelo = SocioModel::query()->create([
            'documento' => $documento,
            'nombre' => $nombre,
        ]);

        return MapeadorDominio::aSocio($modelo);
    }

    public function listarConUltimaMembresia(): array
    {
        return SocioModel::query()
            ->with(['membresias' => fn ($consulta) => $consulta->with('plan')->orderByDesc('id')])
            ->orderBy('nombre')
            ->get()
            ->map(fn (SocioModel $socio): SocioConMembresia => new SocioConMembresia(
                MapeadorDominio::aSocio($socio),
                $socio->membresias->first() ? MapeadorDominio::aMembresia($socio->membresias->first()) : null,
            ))
            ->all();
    }
}
