<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Auth;

use App\Gym\Domain\Entities\Usuario;
use App\Gym\Domain\Ports\EmisorTokenPort;
use App\Gym\Infrastructure\Persistence\Eloquent\MapeadorDominio;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\TokenAccesoModel;
use Illuminate\Support\Str;

final class EmisorTokenBearer implements EmisorTokenPort
{
    private const LONGITUD_TOKEN = 48;

    public function emitirPara(Usuario $usuario): string
    {
        $tokenClaro = Str::random(self::LONGITUD_TOKEN);

        TokenAccesoModel::query()->create([
            'usuario_id' => $usuario->id(),
            'hash_token' => hash('sha256', $tokenClaro),
        ]);

        return $tokenClaro;
    }

    public function buscarUsuarioPorToken(string $tokenClaro): ?Usuario
    {
        $registro = TokenAccesoModel::query()
            ->with('usuario')
            ->where('hash_token', hash('sha256', $tokenClaro))
            ->first();

        return $registro?->usuario ? MapeadorDominio::aUsuario($registro->usuario) : null;
    }

    public function revocar(string $tokenClaro): void
    {
        TokenAccesoModel::query()
            ->where('hash_token', hash('sha256', $tokenClaro))
            ->delete();
    }
}
