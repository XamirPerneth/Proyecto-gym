<?php

declare(strict_types=1);

namespace App\Gym\Domain\ValueObjects;

enum TipoTarifa: string
{
    case MENSUAL = 'MENSUAL';
    case TRIMESTRAL = 'TRIMESTRAL';
    case ANUAL = 'ANUAL';
}
