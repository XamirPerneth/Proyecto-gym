<?php

declare(strict_types=1);

namespace App\Gym\Domain\ValueObjects;

use App\Gym\Domain\Exceptions\ValorInvalidoException;

final class PorcentajeDescuento
{
    public const MINIMO = 0;
    public const MAXIMO = 100;

    private function __construct(private readonly int $valor)
    {
    }

    public static function de(int $valor): self
    {
        if ($valor < self::MINIMO || $valor > self::MAXIMO) {
            throw ValorInvalidoException::porcentajeFueraDeRango($valor, self::MINIMO, self::MAXIMO);
        }

        return new self($valor);
    }

    public function valor(): int
    {
        return $this->valor;
    }
}
