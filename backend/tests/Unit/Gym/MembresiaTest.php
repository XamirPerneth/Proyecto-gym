<?php

declare(strict_types=1);

namespace Tests\Unit\Gym;

use App\Gym\Domain\Entities\Membresia;
use App\Gym\Domain\Entities\Plan;
use App\Gym\Domain\Exceptions\HorarioNoPermitidoException;
use App\Gym\Domain\Exceptions\MembresiaInactivaException;
use App\Gym\Domain\Exceptions\MembresiaNoCongelableException;
use App\Gym\Domain\Exceptions\MembresiaNoCongeladaException;
use App\Gym\Domain\Exceptions\PeriodoCongelamientoInvalidoException;
use App\Gym\Domain\Services\SelectorTarifa;
use App\Gym\Domain\Services\Tarifas\TarifaAnual;
use App\Gym\Domain\Services\Tarifas\TarifaMensual;
use App\Gym\Domain\Services\Tarifas\TarifaTrimestral;
use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\EstadoMembresia;
use App\Gym\Domain\ValueObjects\RangoHorario;
use App\Gym\Domain\ValueObjects\TipoTarifa;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class MembresiaTest extends TestCase
{
    private function planBasico(): Plan
    {
        return new Plan(1, 'Básico', RangoHorario::desdeTexto('06:00', '16:00'), Dinero::de(80000));
    }

    private function planFull(): Plan
    {
        return new Plan(2, 'Full', RangoHorario::desdeTexto('05:00', '23:00'), Dinero::de(130000));
    }

    private function membresiaMensual(Plan $plan, string $inicio = '2026-11-01 10:00'): Membresia
    {
        return Membresia::asignar(1, $plan, new TarifaMensual(), new DateTimeImmutable($inicio));
    }

    public function test_asignar_calcula_fecha_fin_y_precio_con_la_estrategia(): void
    {
        $selector = new SelectorTarifa(new TarifaMensual(), new TarifaTrimestral(), new TarifaAnual());
        $ahora = new DateTimeImmutable('2026-11-01 10:00');

        $trimestral = Membresia::asignar(1, $this->planBasico(), $selector->para(TipoTarifa::TRIMESTRAL), $ahora);
        $anual = Membresia::asignar(1, $this->planFull(), $selector->para(TipoTarifa::ANUAL), $ahora);

        $this->assertSame(216000, $trimestral->precioFinal()->monto());
        $this->assertSame('2027-01-30', $trimestral->fechaFin()->format('Y-m-d'));
        $this->assertSame(1248000, $anual->precioFinal()->monto());
        $this->assertSame('2027-11-01', $anual->fechaFin()->format('Y-m-d'));
    }

    public function test_acceso_permitido_dentro_del_horario(): void
    {
        $this->membresiaMensual($this->planBasico())->validarAcceso(new DateTimeImmutable('2026-11-05 15:59'));

        $this->addToAssertionCount(1);
    }

    public function test_acceso_denegado_fuera_del_horario_del_plan(): void
    {
        $this->expectException(HorarioNoPermitidoException::class);

        $this->membresiaMensual($this->planBasico())->validarAcceso(new DateTimeImmutable('2026-11-05 18:00'));
    }

    public function test_plan_full_permite_horario_nocturno(): void
    {
        $this->membresiaMensual($this->planFull())->validarAcceso(new DateTimeImmutable('2026-11-05 22:30'));

        $this->addToAssertionCount(1);
    }

    public function test_acceso_denegado_cuando_la_membresia_esta_vencida(): void
    {
        $this->expectException(MembresiaInactivaException::class);

        $this->membresiaMensual($this->planFull())->validarAcceso(new DateTimeImmutable('2026-12-15 10:00'));
    }

    public function test_acceso_denegado_cuando_la_membresia_esta_congelada(): void
    {
        $membresia = $this->membresiaMensual($this->planFull());
        $membresia->congelar(10, new DateTimeImmutable('2026-11-05 10:00'));

        $this->expectException(MembresiaInactivaException::class);
        $membresia->validarAcceso(new DateTimeImmutable('2026-11-06 10:00'));
    }

    /** @dataProvider diasInvalidos */
    public function test_congelar_rechaza_dias_fuera_de_rango(int $dias): void
    {
        $this->expectException(PeriodoCongelamientoInvalidoException::class);

        $this->membresiaMensual($this->planFull())->congelar($dias, new DateTimeImmutable('2026-11-05 10:00'));
    }

    public static function diasInvalidos(): array
    {
        return ['menos del minimo' => [6], 'mas del maximo' => [31]];
    }

    public function test_solo_se_congela_una_membresia_activa(): void
    {
        $membresia = $this->membresiaMensual($this->planFull());

        $this->expectException(MembresiaNoCongelableException::class);
        $membresia->congelar(10, new DateTimeImmutable('2027-01-10 10:00'));
    }

    public function test_reactivar_extiende_el_vencimiento_con_los_dias_congelados(): void
    {
        $membresia = $this->membresiaMensual($this->planFull());
        $finOriginal = $membresia->fechaFin();

        $membresia->congelar(15, new DateTimeImmutable('2026-11-05 10:00'));
        $membresia->reactivar();

        $this->assertSame(EstadoMembresia::ACTIVA, $membresia->estadoPersistido());
        $this->assertSame($finOriginal->modify('+15 days')->format('Y-m-d'), $membresia->fechaFin()->format('Y-m-d'));
        $this->assertSame(15, $membresia->diasCongeladosAcumulados());
    }

    public function test_no_se_puede_reactivar_una_membresia_no_congelada(): void
    {
        $this->expectException(MembresiaNoCongeladaException::class);

        $this->membresiaMensual($this->planFull())->reactivar();
    }
}
