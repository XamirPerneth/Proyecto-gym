<?php

declare(strict_types=1);

namespace App\Gym\Application\DTOs;

use App\Gym\Domain\Entities\Membresia;
use DateTimeImmutable;

final class MembresiaDTO
{
    private const FORMATO_FECHA = 'Y-m-d';

    private function __construct(
        public readonly int $id,
        public readonly int $socioId,
        public readonly string $plan,
        public readonly string $horarioPermitido,
        public readonly string $tipoTarifa,
        public readonly string $estado,
        public readonly string $fechaInicio,
        public readonly string $fechaFin,
        public readonly int $precioFinal,
        public readonly int $diasCongelamiento,
        public readonly ?string $fechaCongelamiento,
        public readonly int $diasCongeladosAcumulados,
    ) {
    }

    public static function desde(Membresia $membresia, DateTimeImmutable $ahora): self
    {
        $rango = $membresia->plan()->rangoHorario();

        return new self(
            (int) $membresia->id(),
            $membresia->socioId(),
            $membresia->plan()->nombre(),
            "{$rango->inicio()} - {$rango->fin()}",
            $membresia->tipoTarifa()->value,
            $membresia->estadoEn($ahora)->value,
            $membresia->fechaInicio()->format(self::FORMATO_FECHA),
            $membresia->fechaFin()->format(self::FORMATO_FECHA),
            $membresia->precioFinal()->monto(),
            $membresia->diasCongelamiento(),
            $membresia->fechaCongelamiento()?->format(self::FORMATO_FECHA),
            $membresia->diasCongeladosAcumulados(),
        );
    }

    public function aArreglo(): array
    {
        return get_object_vars($this);
    }
}
