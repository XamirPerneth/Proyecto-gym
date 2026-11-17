<?php

declare(strict_types=1);

namespace App\Gym\Domain\ValueObjects;

use App\Gym\Domain\Exceptions\ValorInvalidoException;

final class Dinero
{
    private const PORCENTAJE_TOTAL = 100;

    private function __construct(private readonly int $monto)
    {
        if ($monto < 0) {
            throw ValorInvalidoException::montoNegativo();
        }
    }

    public static function de(int $monto): self
    {
        return new self($monto);
    }

    public function multiplicarPor(int $factor): self
    {
        return new self($this->monto * $factor);
    }

    public function conDescuento(int $porcentaje): self
    {
        return new self(intdiv($this->monto * (self::PORCENTAJE_TOTAL - $porcentaje), self::PORCENTAJE_TOTAL));
    }

    public function monto(): int
    {
        return $this->monto;
    }
}
