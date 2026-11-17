<?php

declare(strict_types=1);

namespace App\Gym\Domain\Exceptions;

final class ValorInvalidoException extends DominioException
{
    public static function documentoVacio(): self
    {
        return new self('El documento del socio es obligatorio.');
    }

    public static function nombreSocioVacio(): self
    {
        return new self('El nombre del socio es obligatorio.');
    }

    public static function nombreGimnasioVacio(): self
    {
        return new self('El nombre del gimnasio es obligatorio.');
    }

    public static function horaMalFormada(string $hora): self
    {
        return new self("La hora '{$hora}' no tiene el formato HH:MM.");
    }

    public static function horaSimuladaInvalida(string $valor): self
    {
        return new self("La hora simulada '{$valor}' no es válida; usa el formato AAAA-MM-DD HH:MM.");
    }

    public static function rangoHorarioIncoherente(): self
    {
        return new self('La hora de inicio del rango horario debe ser anterior a la hora de fin.');
    }

    public static function montoNegativo(): self
    {
        return new self('El monto no puede ser negativo.');
    }

    public static function porcentajeFueraDeRango(int $valor, int $minimo, int $maximo): self
    {
        return new self("El porcentaje de descuento debe estar entre {$minimo} y {$maximo}; se recibió {$valor}.");
    }
}
