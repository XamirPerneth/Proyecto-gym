<?php

declare(strict_types=1);

namespace App\Gym\Domain\Entities;

final class Usuario
{
    public function __construct(
        private readonly int $id,
        private readonly string $nombre,
        private readonly string $email,
        private readonly string $rol,
        private readonly string $hashContrasena,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function rol(): string
    {
        return $this->rol;
    }

    public function hashContrasena(): string
    {
        return $this->hashContrasena;
    }
}
