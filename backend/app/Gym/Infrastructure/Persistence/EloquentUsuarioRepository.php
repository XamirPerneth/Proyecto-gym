<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence;

use App\Gym\Domain\Entities\Usuario;
use App\Gym\Domain\Ports\UsuarioLecturaPort;
use App\Gym\Infrastructure\Persistence\Eloquent\MapeadorDominio;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\UsuarioGimnasioModel;

final class EloquentUsuarioRepository implements UsuarioLecturaPort
{
    public function buscarPorEmail(string $email): ?Usuario
    {
        $modelo = UsuarioGimnasioModel::query()->where('email', $email)->first();

        return $modelo ? MapeadorDominio::aUsuario($modelo) : null;
    }
}
