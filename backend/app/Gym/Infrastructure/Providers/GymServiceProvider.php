<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Providers;

use App\Gym\Domain\Ports\AjustesGimnasioPort;
use App\Gym\Domain\Ports\ConsultaAccesosPort;
use App\Gym\Domain\Ports\ConsultaEstadisticasPort;
use App\Gym\Domain\Ports\ConsultaSociosPort;
use App\Gym\Domain\Ports\EmisorTokenPort;
use App\Gym\Domain\Ports\HasherContrasenaPort;
use App\Gym\Domain\Ports\MembresiaEscrituraPort;
use App\Gym\Domain\Ports\MembresiaLecturaPort;
use App\Gym\Domain\Ports\PlanEscrituraPort;
use App\Gym\Domain\Ports\PlanLecturaPort;
use App\Gym\Domain\Ports\RegistroAccesoEscrituraPort;
use App\Gym\Domain\Ports\RelojPort;
use App\Gym\Domain\Ports\SocioEscrituraPort;
use App\Gym\Domain\Ports\SocioLecturaPort;
use App\Gym\Domain\Ports\UsuarioLecturaPort;
use App\Gym\Domain\Services\SelectorTarifa;
use App\Gym\Domain\Services\Tarifas\TarifaAnual;
use App\Gym\Domain\Services\Tarifas\TarifaMensual;
use App\Gym\Domain\Services\Tarifas\TarifaTrimestral;
use App\Gym\Infrastructure\Auth\EmisorTokenBearer;
use App\Gym\Infrastructure\Auth\HasherLaravel;
use App\Gym\Infrastructure\Clock\RelojSistema;
use App\Gym\Infrastructure\Http\ManejadorExcepcionesDominio;
use App\Gym\Infrastructure\Persistence\EloquentAjustesGimnasioRepository;
use App\Gym\Infrastructure\Persistence\EloquentEstadisticasRepository;
use App\Gym\Infrastructure\Persistence\EloquentMembresiaRepository;
use App\Gym\Infrastructure\Persistence\EloquentPlanRepository;
use App\Gym\Infrastructure\Persistence\EloquentRegistroAccesoRepository;
use App\Gym\Infrastructure\Persistence\EloquentSocioRepository;
use App\Gym\Infrastructure\Persistence\EloquentUsuarioRepository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

/** Enlaza cada Port del dominio con su Adapter de infraestructura (DIP). */
final class GymServiceProvider extends ServiceProvider
{
    private const ETIQUETA_TARIFAS = 'gym.tarifas';

    private const ESTRATEGIAS_TARIFA = [
        TarifaMensual::class,
        TarifaTrimestral::class,
        TarifaAnual::class,
    ];

    public function register(): void
    {
        $this->registrarPersistencia();
        $this->registrarSeguridad();
        $this->registrarTarifas();

        $this->app->singleton(RelojPort::class, fn ($app): RelojPort => new RelojSistema(
            (string) config('gym.zona_horaria'),
            config('gym.hora_simulada'),
            $app->make(AjustesGimnasioPort::class),
        ));
    }

    public function boot(): void
    {
        $manejador = $this->app->make(ExceptionHandler::class);

        if (method_exists($manejador, 'renderable')) {
            $manejador->renderable(ManejadorExcepcionesDominio::comoCallback());
        }
    }

    private function registrarPersistencia(): void
    {
        $this->app->singleton(EloquentSocioRepository::class);
        $this->app->bind(SocioLecturaPort::class, EloquentSocioRepository::class);
        $this->app->bind(SocioEscrituraPort::class, EloquentSocioRepository::class);
        $this->app->bind(ConsultaSociosPort::class, EloquentSocioRepository::class);

        $this->app->singleton(EloquentPlanRepository::class);
        $this->app->bind(PlanLecturaPort::class, EloquentPlanRepository::class);
        $this->app->bind(PlanEscrituraPort::class, EloquentPlanRepository::class);

        $this->app->bind(MembresiaLecturaPort::class, EloquentMembresiaRepository::class);
        $this->app->bind(MembresiaEscrituraPort::class, EloquentMembresiaRepository::class);

        $this->app->singleton(EloquentRegistroAccesoRepository::class);
        $this->app->bind(RegistroAccesoEscrituraPort::class, EloquentRegistroAccesoRepository::class);
        $this->app->bind(ConsultaAccesosPort::class, EloquentRegistroAccesoRepository::class);

        $this->app->bind(UsuarioLecturaPort::class, EloquentUsuarioRepository::class);
        $this->app->bind(AjustesGimnasioPort::class, EloquentAjustesGimnasioRepository::class);
        $this->app->bind(ConsultaEstadisticasPort::class, EloquentEstadisticasRepository::class);
    }

    private function registrarSeguridad(): void
    {
        $this->app->bind(HasherContrasenaPort::class, HasherLaravel::class);
        $this->app->bind(EmisorTokenPort::class, EmisorTokenBearer::class);
    }

    private function registrarTarifas(): void
    {
        $this->app->tag(self::ESTRATEGIAS_TARIFA, self::ETIQUETA_TARIFAS);

        $this->app->bind(
            SelectorTarifa::class,
            fn ($app): SelectorTarifa => new SelectorTarifa(...$app->tagged(self::ETIQUETA_TARIFAS)),
        );
    }
}
