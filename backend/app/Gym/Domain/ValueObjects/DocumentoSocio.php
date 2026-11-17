<?php

declare(strict_types=1);

namespace App\Gym\Domain\ValueObjects;

use App\Gym\Domain\Exceptions\ValorInvalidoException;

final class DocumentoSocio
{
    private readonly string $valor;

    public function __construct(string $valor)
    {
        $limpio = trim($valor);

        if ($limpio === '') {
            throw ValorInvalidoException::documentoVacio();
        }

        $this->valor = $limpio;
    }

    public function valor(): string
    {
        return $this->valor;
    }
}
