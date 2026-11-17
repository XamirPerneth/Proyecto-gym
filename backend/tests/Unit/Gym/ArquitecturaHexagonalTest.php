<?php

declare(strict_types=1);

namespace Tests\Unit\Gym;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Guardián de la arquitectura hexagonal: Domain y Application deben ser PHP puro.
 */
final class ArquitecturaHexagonalTest extends TestCase
{
    private const CAPAS_PURAS = ['Domain', 'Application'];

    public function test_dominio_y_aplicacion_no_dependen_del_framework(): void
    {
        $infracciones = [];

        foreach (self::CAPAS_PURAS as $capa) {
            foreach ($this->archivosPhp($capa) as $archivo) {
                $infracciones = array_merge($infracciones, $this->importsProhibidos($archivo));
            }
        }

        $this->assertSame([], $infracciones, "Imports de framework en capas puras:\n" . implode("\n", $infracciones));
    }

    public function test_aplicacion_no_depende_de_infraestructura(): void
    {
        $infracciones = [];

        foreach (['Domain', 'Application'] as $capa) {
            foreach ($this->archivosPhp($capa) as $archivo) {
                if (str_contains((string) file_get_contents($archivo), 'App\\Gym\\Infrastructure')) {
                    $infracciones[] = $archivo;
                }
            }
        }

        $this->assertSame([], $infracciones, 'Dominio/Aplicación no pueden depender de Infrastructure.');
    }

    /** @return string[] */
    private function importsProhibidos(string $archivo): array
    {
        $patron = '/^\s*use\s+(\\\\?Illuminate\\\\|\\\\?App\\\\Models\\\\)|\bIlluminate\\\\/m';

        return preg_match($patron, (string) file_get_contents($archivo)) === 1 ? [$archivo] : [];
    }

    /** @return string[] */
    private function archivosPhp(string $capa): array
    {
        $raiz = dirname(__DIR__, 3) . '/app/Gym/' . $capa;
        $iterador = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($raiz));
        $archivos = [];

        foreach ($iterador as $archivo) {
            if ($archivo->isFile() && $archivo->getExtension() === 'php') {
                $archivos[] = $archivo->getPathname();
            }
        }

        return $archivos;
    }
}
