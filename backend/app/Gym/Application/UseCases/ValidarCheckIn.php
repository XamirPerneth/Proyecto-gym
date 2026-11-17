<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\CheckInResultadoDTO;
use App\Gym\Domain\Entities\Membresia;
use App\Gym\Domain\Entities\RegistroAcceso;
use App\Gym\Domain\Entities\Socio;
use App\Gym\Domain\Exceptions\DominioException;
use App\Gym\Domain\Exceptions\MembresiaInactivaException;
use App\Gym\Domain\Exceptions\SocioNoEncontradoException;
use App\Gym\Domain\Ports\MembresiaLecturaPort;
use App\Gym\Domain\Ports\RegistroAccesoEscrituraPort;
use App\Gym\Domain\Ports\RelojPort;
use App\Gym\Domain\Ports\SocioLecturaPort;
use App\Gym\Domain\ValueObjects\DocumentoSocio;
use DateTimeImmutable;

final class ValidarCheckIn
{
    private const FORMATO_FECHA = 'Y-m-d';
    private const FORMATO_HORA = 'H:i:s';

    public function __construct(
        private readonly SocioLecturaPort $socios,
        private readonly MembresiaLecturaPort $membresias,
        private readonly RegistroAccesoEscrituraPort $registros,
        private readonly RelojPort $reloj,
    ) {
    }

    /**
     * @throws SocioNoEncontradoException
     * @throws DominioException Cuando el acceso es denegado por la membresía o el horario.
     */
    public function ejecutar(string $documento): CheckInResultadoDTO
    {
        $ahora = $this->reloj->ahora();
        $documentoSocio = new DocumentoSocio($documento);

        $socio = $this->socios->buscarPorDocumento($documentoSocio->valor())
            ?? throw SocioNoEncontradoException::conDocumento($documentoSocio->valor());

        $membresia = $this->membresias->buscarUltimaPorSocio($socio->id());

        try {
            $this->validar($membresia, $ahora);
        } catch (DominioException $motivo) {
            $this->registros->registrar(
                RegistroAcceso::denegado($socio->id(), $membresia?->id(), $ahora, $motivo->getMessage())
            );
            throw $motivo;
        }

        $this->registros->registrar(RegistroAcceso::permitido($socio->id(), (int) $membresia->id(), $ahora));

        return $this->resultadoPermitido($socio, $membresia, $ahora);
    }

    private function validar(?Membresia $membresia, DateTimeImmutable $ahora): void
    {
        if ($membresia === null) {
            throw MembresiaInactivaException::sinMembresia();
        }

        $membresia->validarAcceso($ahora);
    }

    private function resultadoPermitido(Socio $socio, Membresia $membresia, DateTimeImmutable $ahora): CheckInResultadoDTO
    {
        $rango = $membresia->plan()->rangoHorario();
        $hoy = $ahora->setTime(0, 0);
        $diasRestantes = max(0, (int) $hoy->diff($membresia->fechaFin())->format('%r%a'));

        return new CheckInResultadoDTO(
            true,
            $socio->nombre(),
            $socio->documento(),
            $membresia->plan()->nombre(),
            "{$rango->inicio()} - {$rango->fin()}",
            $membresia->fechaFin()->format(self::FORMATO_FECHA),
            $diasRestantes,
            $ahora->format(self::FORMATO_HORA),
        );
    }
}
