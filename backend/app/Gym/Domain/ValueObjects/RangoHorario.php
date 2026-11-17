<?php

declare(strict_types=1);

namespace App\Gym\Domain\ValueObjects;

use App\Gym\Domain\Exceptions\ValorInvalidoException;
use DateTimeInterface;

final class RangoHorario
{
    private const SEGUNDOS_POR_MINUTO = 60;
    private const SEGUNDOS_POR_HORA = 3600;
    private const FORMATO_HORA = '/^([01]\d|2[0-3]):([0-5]\d)(?::[0-5]\d)?$/';

    private function __construct(
        private readonly int $inicioEnSegundos,
        private readonly int $finEnSegundos,
        private readonly string $inicioTexto,
        private readonly string $finTexto,
    ) {
        if ($inicioEnSegundos >= $finEnSegundos) {
            throw ValorInvalidoException::rangoHorarioIncoherente();
        }
    }

    public static function desdeTexto(string $inicio, string $fin): self
    {
        return new self(
            self::aSegundos($inicio),
            self::aSegundos($fin),
            substr($inicio, 0, 5),
            substr($fin, 0, 5),
        );
    }

    public function contiene(DateTimeInterface $momento): bool
    {
        $segundos = (int) $momento->format('G') * self::SEGUNDOS_POR_HORA
            + (int) $momento->format('i') * self::SEGUNDOS_POR_MINUTO
            + (int) $momento->format('s');

        return $segundos >= $this->inicioEnSegundos && $segundos <= $this->finEnSegundos;
    }

    public function inicio(): string
    {
        return $this->inicioTexto;
    }

    public function fin(): string
    {
        return $this->finTexto;
    }

    public function descripcion(): string
    {
        return "{$this->inicioTexto} y {$this->finTexto}";
    }

    private static function aSegundos(string $hora): int
    {
        if (preg_match(self::FORMATO_HORA, $hora, $partes) !== 1) {
            throw ValorInvalidoException::horaMalFormada($hora);
        }

        return (int) $partes[1] * self::SEGUNDOS_POR_HORA + (int) $partes[2] * self::SEGUNDOS_POR_MINUTO;
    }
}
