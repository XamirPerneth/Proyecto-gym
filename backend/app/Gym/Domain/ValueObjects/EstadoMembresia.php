<?php

declare(strict_types=1);

namespace App\Gym\Domain\ValueObjects;

enum EstadoMembresia: string
{
    case ACTIVA = 'ACTIVA';
    case VENCIDA = 'VENCIDA';
    case CONGELADA = 'CONGELADA';

    public function etiqueta(): string
    {
        return $this->value;
    }
}
