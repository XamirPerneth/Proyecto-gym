<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\SocioResumenDTO;
use App\Gym\Domain\Exceptions\SocioYaRegistradoException;
use App\Gym\Domain\Exceptions\ValorInvalidoException;
use App\Gym\Domain\Ports\SocioEscrituraPort;
use App\Gym\Domain\Ports\SocioLecturaPort;
use App\Gym\Domain\ValueObjects\DocumentoSocio;

final class RegistrarSocio
{
    public function __construct(
        private readonly SocioLecturaPort $lectura,
        private readonly SocioEscrituraPort $escritura,
    ) {
    }

    /**
     * @throws SocioYaRegistradoException
     * @throws ValorInvalidoException
     */
    public function ejecutar(string $documento, string $nombre): SocioResumenDTO
    {
        $documentoVo = new DocumentoSocio($documento);
        $nombreLimpio = trim($nombre);

        if ($nombreLimpio === '') {
            throw ValorInvalidoException::nombreSocioVacio();
        }

        if ($this->lectura->buscarPorDocumento($documentoVo->valor()) !== null) {
            throw SocioYaRegistradoException::conDocumento($documentoVo->valor());
        }

        $socio = $this->escritura->registrar($documentoVo->valor(), $nombreLimpio);

        return new SocioResumenDTO($socio->id(), $socio->documento(), $socio->nombre(), null);
    }
}
