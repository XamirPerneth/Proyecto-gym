<?php

declare(strict_types=1);

namespace App\Gym\Domain\Services;

use App\Gym\Domain\Exceptions\TarifaNoSoportadaException;
use App\Gym\Domain\Ports\CalculadoraTarifaPort;
use App\Gym\Domain\ValueObjects\TipoTarifa;

/** Resuelve la estrategia de tarifa; agregar un plan nuevo solo requiere registrar otra calculadora. */
final class SelectorTarifa
{
    /** @var array<string, CalculadoraTarifaPort> */
    private array $calculadoras = [];

    public function __construct(CalculadoraTarifaPort ...$calculadoras)
    {
        foreach ($calculadoras as $calculadora) {
            $this->calculadoras[$calculadora->tipo()->value] = $calculadora;
        }
    }

    public function para(TipoTarifa $tipo): CalculadoraTarifaPort
    {
        return $this->calculadoras[$tipo->value] ?? throw TarifaNoSoportadaException::para($tipo->value);
    }

    /** @return CalculadoraTarifaPort[] */
    public function todas(): array
    {
        return array_values($this->calculadoras);
    }
}
