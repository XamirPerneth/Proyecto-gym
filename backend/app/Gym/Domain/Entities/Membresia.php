<?php

declare(strict_types=1);

namespace App\Gym\Domain\Entities;

use App\Gym\Domain\Exceptions\HorarioNoPermitidoException;
use App\Gym\Domain\Exceptions\MembresiaInactivaException;
use App\Gym\Domain\Exceptions\MembresiaNoCongelableException;
use App\Gym\Domain\Exceptions\MembresiaNoCongeladaException;
use App\Gym\Domain\Ports\CalculadoraTarifaPort;
use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\EstadoMembresia;
use App\Gym\Domain\ValueObjects\PeriodoCongelamiento;
use App\Gym\Domain\ValueObjects\TipoTarifa;
use DateTimeImmutable;

final class Membresia
{
    public function __construct(
        private readonly ?int $id,
        private readonly int $socioId,
        private readonly Plan $plan,
        private readonly TipoTarifa $tipoTarifa,
        private EstadoMembresia $estado,
        private readonly DateTimeImmutable $fechaInicio,
        private DateTimeImmutable $fechaFin,
        private readonly Dinero $precioFinal,
        private ?DateTimeImmutable $fechaCongelamiento = null,
        private int $diasCongelamiento = 0,
        private int $diasCongeladosAcumulados = 0,
    ) {
    }

    public static function asignar(
        int $socioId,
        Plan $plan,
        CalculadoraTarifaPort $tarifa,
        DateTimeImmutable $ahora,
    ): self {
        $inicio = self::soloFecha($ahora);

        return new self(
            null,
            $socioId,
            $plan,
            $tarifa->tipo(),
            EstadoMembresia::ACTIVA,
            $inicio,
            $inicio->modify("+{$tarifa->duracionEnDias()} days"),
            $tarifa->calcularPrecio($plan->precioBaseMensual()),
        );
    }

    /**
     * Regla de check-in: la membresía debe estar ACTIVA y la hora dentro del rango del plan.
     *
     * @throws MembresiaInactivaException
     * @throws HorarioNoPermitidoException
     */
    public function validarAcceso(DateTimeImmutable $ahora): void
    {
        $estado = $this->estadoEn($ahora);

        if ($estado !== EstadoMembresia::ACTIVA) {
            throw MembresiaInactivaException::porEstado($estado);
        }

        if (!$this->plan->rangoHorario()->contiene($ahora)) {
            throw HorarioNoPermitidoException::fueraDeRango(
                $this->plan->nombre(),
                $this->plan->rangoHorario(),
                $ahora->format('H:i'),
            );
        }
    }

    /**
     * @throws MembresiaNoCongelableException
     * @throws \App\Gym\Domain\Exceptions\PeriodoCongelamientoInvalidoException
     */
    public function congelar(int $dias, DateTimeImmutable $ahora): void
    {
        $estado = $this->estadoEn($ahora);

        if ($estado !== EstadoMembresia::ACTIVA) {
            throw MembresiaNoCongelableException::porEstado($estado->etiqueta());
        }

        $periodo = PeriodoCongelamiento::deDias($dias);

        $this->estado = EstadoMembresia::CONGELADA;
        $this->fechaCongelamiento = self::soloFecha($ahora);
        $this->diasCongelamiento = $periodo->dias();
    }

    /**
     * Reactiva la membresía y extiende su vencimiento con los días congelados.
     *
     * @throws MembresiaNoCongeladaException
     */
    public function reactivar(): void
    {
        if ($this->estado !== EstadoMembresia::CONGELADA) {
            throw MembresiaNoCongeladaException::paraReactivar();
        }

        $this->fechaFin = $this->fechaFin->modify("+{$this->diasCongelamiento} days");
        $this->diasCongeladosAcumulados += $this->diasCongelamiento;
        $this->estado = EstadoMembresia::ACTIVA;
        $this->fechaCongelamiento = null;
        $this->diasCongelamiento = 0;
    }

    /** El estado persistido ACTIVA pasa a VENCIDA cuando la fecha de fin ya quedó atrás. */
    public function estadoEn(DateTimeImmutable $ahora): EstadoMembresia
    {
        if ($this->estado === EstadoMembresia::ACTIVA && $this->fechaFin < self::soloFecha($ahora)) {
            return EstadoMembresia::VENCIDA;
        }

        return $this->estado;
    }

    public function impideNuevaAdquisicion(DateTimeImmutable $ahora): bool
    {
        return $this->estadoEn($ahora) !== EstadoMembresia::VENCIDA;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function socioId(): int
    {
        return $this->socioId;
    }

    public function plan(): Plan
    {
        return $this->plan;
    }

    public function tipoTarifa(): TipoTarifa
    {
        return $this->tipoTarifa;
    }

    public function estadoPersistido(): EstadoMembresia
    {
        return $this->estado;
    }

    public function fechaInicio(): DateTimeImmutable
    {
        return $this->fechaInicio;
    }

    public function fechaFin(): DateTimeImmutable
    {
        return $this->fechaFin;
    }

    public function precioFinal(): Dinero
    {
        return $this->precioFinal;
    }

    public function fechaCongelamiento(): ?DateTimeImmutable
    {
        return $this->fechaCongelamiento;
    }

    public function diasCongelamiento(): int
    {
        return $this->diasCongelamiento;
    }

    public function diasCongeladosAcumulados(): int
    {
        return $this->diasCongeladosAcumulados;
    }

    public function conId(int $id): self
    {
        $copia = clone $this;

        return new self(
            $id,
            $copia->socioId,
            $copia->plan,
            $copia->tipoTarifa,
            $copia->estado,
            $copia->fechaInicio,
            $copia->fechaFin,
            $copia->precioFinal,
            $copia->fechaCongelamiento,
            $copia->diasCongelamiento,
            $copia->diasCongeladosAcumulados,
        );
    }

    private static function soloFecha(DateTimeImmutable $momento): DateTimeImmutable
    {
        return $momento->setTime(0, 0);
    }
}
