<?php

declare(strict_types=1);

namespace App\Gym\Domain\ValueObjects;

use App\Gym\Domain\Exceptions\PeriodoCongelamientoInvalidoException;

final class PeriodoCongelamiento
{
    public const DIAS_MINIMOS = 7;
    public const DIAS_MAXIMOS = 30;

    private function __construct(private readonly int $dias)
    {
    }

    public static function deDias(int $dias): self
    {
        if ($dias < self::DIAS_MINIMOS || $dias > self::DIAS_MAXIMOS) {
            throw PeriodoCongelamientoInvalidoException::fueraDeLimites(
                $dias,
                self::DIAS_MINIMOS,
                self::DIAS_MAXIMOS,
            );
        }

        return new self($dias);
    }

    public function dias(): int
    {
        return $this->dias;
    }
}
