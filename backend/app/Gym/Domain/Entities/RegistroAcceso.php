<?php

declare(strict_types=1);

namespace App\Gym\Domain\Entities;

use DateTimeImmutable;

final class RegistroAcceso
{
    private function __construct(
        private readonly int $socioId,
        private readonly ?int $membresiaId,
        private readonly DateTimeImmutable $fechaHora,
        private readonly bool $permitido,
        private readonly ?string $motivo,
    ) {
    }

    public static function permitido(int $socioId, int $membresiaId, DateTimeImmutable $fechaHora): self
    {
        return new self($socioId, $membresiaId, $fechaHora, true, null);
    }

    public static function denegado(
        int $socioId,
        ?int $membresiaId,
        DateTimeImmutable $fechaHora,
        string $motivo,
    ): self {
        return new self($socioId, $membresiaId, $fechaHora, false, $motivo);
    }

    public function socioId(): int
    {
        return $this->socioId;
    }

    public function membresiaId(): ?int
    {
        return $this->membresiaId;
    }

    public function fechaHora(): DateTimeImmutable
    {
        return $this->fechaHora;
    }

    public function fuePermitido(): bool
    {
        return $this->permitido;
    }

    public function motivo(): ?string
    {
        return $this->motivo;
    }
}
