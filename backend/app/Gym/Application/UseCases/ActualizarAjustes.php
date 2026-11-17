<?php

declare(strict_types=1);

namespace App\Gym\Application\UseCases;

use App\Gym\Application\DTOs\ActualizarAjustesComando;
use App\Gym\Application\DTOs\AjustesGimnasioDTO;
use App\Gym\Domain\Entities\AjustesGimnasio;
use App\Gym\Domain\Exceptions\PlanNoEncontradoException;
use App\Gym\Domain\Exceptions\ValorInvalidoException;
use App\Gym\Domain\Ports\AjustesGimnasioPort;
use App\Gym\Domain\Ports\PlanEscrituraPort;
use App\Gym\Domain\Ports\PlanLecturaPort;
use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\PorcentajeDescuento;
use App\Gym\Domain\ValueObjects\RangoHorario;
use DateTimeImmutable;
use Throwable;

final class ActualizarAjustes
{
    public function __construct(
        private readonly AjustesGimnasioPort $ajustes,
        private readonly PlanLecturaPort $lecturaPlanes,
        private readonly PlanEscrituraPort $escrituraPlanes,
    ) {
    }

    /**
     * @throws PlanNoEncontradoException
     * @throws ValorInvalidoException
     */
    public function ejecutar(ActualizarAjustesComando $comando): AjustesGimnasioDTO
    {
        $horaSimulada = $this->parsearHoraSimulada($comando->horaSimulada);

        $nuevosAjustes = new AjustesGimnasio(
            trim($comando->nombreGimnasio),
            PorcentajeDescuento::de($comando->descuentoTrimestral),
            PorcentajeDescuento::de($comando->descuentoAnual),
            $horaSimulada,
        );

        foreach ($comando->planes as $datosPlan) {
            $idPlan = (int) $datosPlan['id'];
            if ($this->lecturaPlanes->buscarPorId($idPlan) === null) {
                throw PlanNoEncontradoException::conId($idPlan);
            }

            $rango = RangoHorario::desdeTexto((string) $datosPlan['hora_inicio'], (string) $datosPlan['hora_fin']);
            $precio = Dinero::de((int) $datosPlan['precio_base_mensual']);
            $this->escrituraPlanes->actualizarConfiguracion($idPlan, $rango, $precio);
        }

        $guardados = $this->ajustes->guardar($nuevosAjustes);

        return AjustesGimnasioDTO::desde($guardados, $this->lecturaPlanes->listar());
    }

    private function parsearHoraSimulada(?string $valor): ?DateTimeImmutable
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }

        $normalizado = str_replace('T', ' ', trim($valor));

        try {
            return new DateTimeImmutable($normalizado);
        } catch (Throwable) {
            throw ValorInvalidoException::horaSimuladaInvalida($valor);
        }
    }
}
