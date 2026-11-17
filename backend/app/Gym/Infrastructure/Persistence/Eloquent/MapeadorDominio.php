<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence\Eloquent;

use App\Gym\Domain\Entities\Membresia;
use App\Gym\Domain\Entities\Plan;
use App\Gym\Domain\Entities\Socio;
use App\Gym\Domain\Entities\Usuario;
use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\EstadoMembresia;
use App\Gym\Domain\ValueObjects\RangoHorario;
use App\Gym\Domain\ValueObjects\TipoTarifa;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\MembresiaModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\PlanModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\SocioModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\UsuarioGimnasioModel;
use DateTimeImmutable;

/** Traduce entre modelos Eloquent y entidades puras del dominio. */
final class MapeadorDominio
{
    public static function aSocio(SocioModel $modelo): Socio
    {
        return new Socio($modelo->id, $modelo->documento, $modelo->nombre);
    }

    public static function aPlan(PlanModel $modelo): Plan
    {
        return new Plan(
            $modelo->id,
            $modelo->nombre,
            RangoHorario::desdeTexto($modelo->hora_inicio, $modelo->hora_fin),
            Dinero::de($modelo->precio_base_mensual),
        );
    }

    public static function aMembresia(MembresiaModel $modelo): Membresia
    {
        return new Membresia(
            $modelo->id,
            $modelo->socio_id,
            self::aPlan($modelo->plan),
            TipoTarifa::from($modelo->tipo_tarifa),
            EstadoMembresia::from($modelo->estado),
            self::fecha($modelo->fecha_inicio),
            self::fecha($modelo->fecha_fin),
            Dinero::de($modelo->precio_final),
            $modelo->fecha_congelamiento ? self::fecha($modelo->fecha_congelamiento) : null,
            $modelo->dias_congelamiento,
            $modelo->dias_congelados_acumulados,
        );
    }

    public static function aUsuario(UsuarioGimnasioModel $modelo): Usuario
    {
        return new Usuario(
            $modelo->id,
            $modelo->nombre,
            $modelo->email,
            $modelo->rol,
            $modelo->password,
        );
    }

    private static function fecha(\DateTimeInterface $fecha): DateTimeImmutable
    {
        return new DateTimeImmutable($fecha->format('Y-m-d'), $fecha->getTimezone());
    }
}
