<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\AsignarMembresiaComando;
use App\Gym\Application\DTOs\MembresiaDTO;
use App\Gym\Domain\Entities\Membresia;
use App\Gym\Domain\Entities\Socio;
use App\Gym\Domain\Exceptions\MembresiaActivaExistenteException;
use App\Gym\Domain\Exceptions\PlanNoEncontradoException;
use App\Gym\Domain\Exceptions\SocioNoEncontradoException;
use App\Gym\Domain\Exceptions\TarifaNoSoportadaException;
use App\Gym\Domain\Ports\MembresiaEscrituraPort;
use App\Gym\Domain\Ports\MembresiaLecturaPort;
use App\Gym\Domain\Ports\PlanLecturaPort;
use App\Gym\Domain\Ports\RelojPort;
use App\Gym\Domain\Ports\SocioLecturaPort;
use App\Gym\Domain\Services\SelectorTarifa;
use App\Gym\Domain\ValueObjects\TipoTarifa;
use DateTimeImmutable;

final class AsignarMembresia
{
    public function __construct(
        private readonly SocioLecturaPort $socios,
        private readonly PlanLecturaPort $planes,
        private readonly MembresiaLecturaPort $lecturaMembresias,
        private readonly MembresiaEscrituraPort $escrituraMembresias,
        private readonly SelectorTarifa $tarifas,
        private readonly RelojPort $reloj,
    ) {
    }

    /**
     * @throws SocioNoEncontradoException
     * @throws PlanNoEncontradoException
     * @throws MembresiaActivaExistenteException
     * @throws TarifaNoSoportadaException
     */
    public function ejecutar(AsignarMembresiaComando $comando): MembresiaDTO
    {
        $ahora = $this->reloj->ahora();

        $socio = $this->socios->buscarPorId($comando->socioId)
            ?? throw SocioNoEncontradoException::conId($comando->socioId);
        $plan = $this->planes->buscarPorId($comando->planId)
            ?? throw PlanNoEncontradoException::conId($comando->planId);
        $tarifa = $this->tarifas->para($this->tipoTarifaDe($comando->tipoTarifa));

        $this->garantizarSinMembresiaVigente($socio, $ahora);

        $membresia = $this->escrituraMembresias->guardar(
            Membresia::asignar($socio->id(), $plan, $tarifa, $ahora)
        );

        return MembresiaDTO::desde($membresia, $ahora);
    }

    private function tipoTarifaDe(string $valor): TipoTarifa
    {
        return TipoTarifa::tryFrom($valor) ?? throw TarifaNoSoportadaException::para($valor);
    }

    private function garantizarSinMembresiaVigente(Socio $socio, DateTimeImmutable $ahora): void
    {
        $ultima = $this->lecturaMembresias->buscarUltimaPorSocio($socio->id());

        if ($ultima !== null && $ultima->impideNuevaAdquisicion($ahora)) {
            throw MembresiaActivaExistenteException::paraSocio($socio->nombre());
        }
    }
}
