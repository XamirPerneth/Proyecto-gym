<?php

declare(strict_types=1);

namespace Tests\Unit\Gym;

use App\Gym\Application\UseCases\AutenticarUsuarioUseCase;
use App\Gym\Application\UseCases\RegistrarSocio;
use App\Gym\Domain\Entities\AjustesGimnasio;
use App\Gym\Domain\Entities\Socio;
use App\Gym\Domain\Entities\Usuario;
use App\Gym\Domain\Exceptions\CredencialesInvalidasException;
use App\Gym\Domain\Exceptions\SocioYaRegistradoException;
use App\Gym\Domain\Ports\AjustesGimnasioPort;
use App\Gym\Domain\Ports\EmisorTokenPort;
use App\Gym\Domain\Ports\HasherContrasenaPort;
use App\Gym\Domain\Ports\SocioEscrituraPort;
use App\Gym\Domain\Ports\SocioLecturaPort;
use App\Gym\Domain\Ports\UsuarioLecturaPort;
use App\Gym\Domain\Services\Tarifas\TarifaAnual;
use App\Gym\Domain\Services\Tarifas\TarifaTrimestral;
use App\Gym\Domain\ValueObjects\Dinero;
use App\Gym\Domain\ValueObjects\PorcentajeDescuento;
use PHPUnit\Framework\TestCase;

final class Fase2CasosDeUsoTest extends TestCase
{
    public function test_autenticar_usuario_emite_token_cuando_las_credenciales_son_validas(): void
    {
        $usuario = new Usuario(1, 'Admin Demo', 'admin@gymcontrol.com', 'Administrador', 'hash_secreto');

        $lectura = new class($usuario) implements UsuarioLecturaPort {
            public function __construct(private readonly Usuario $u) {}
            public function buscarPorEmail(string $email): ?Usuario {
                return $email === $this->u->email() ? $this->u : null;
            }
        };

        $hasher = new class implements HasherContrasenaPort {
            public function verificar(string $plana, string $hash): bool {
                return $plana === 'admin123' && $hash === 'hash_secreto';
            }
        };

        $tokens = new class implements EmisorTokenPort {
            public function emitirPara(Usuario $usuario): string { return 'token_emitido_123'; }
            public function buscarUsuarioPorToken(string $tokenClaro): ?Usuario { return null; }
            public function revocar(string $tokenClaro): void {}
        };

        $useCase = new AutenticarUsuarioUseCase($lectura, $hasher, $tokens);
        $sesion = $useCase->ejecutar('ADMIN@gymcontrol.com', 'admin123');

        $this->assertSame('token_emitido_123', $sesion->token);
        $this->assertSame('admin@gymcontrol.com', $sesion->usuario->email);
    }

    public function test_autenticar_usuario_lanza_excepcion_si_la_contrasena_es_incorrecta(): void
    {
        $usuario = new Usuario(1, 'Admin Demo', 'admin@gymcontrol.com', 'Administrador', 'hash_secreto');

        $lectura = new class($usuario) implements UsuarioLecturaPort {
            public function __construct(private readonly Usuario $u) {}
            public function buscarPorEmail(string $email): ?Usuario { return $this->u; }
        };

        $hasher = new class implements HasherContrasenaPort {
            public function verificar(string $plana, string $hash): bool { return false; }
        };

        $tokens = new class implements EmisorTokenPort {
            public function emitirPara(Usuario $usuario): string { return 'token'; }
            public function buscarUsuarioPorToken(string $tokenClaro): ?Usuario { return null; }
            public function revocar(string $tokenClaro): void {}
        };

        $this->expectException(CredencialesInvalidasException::class);

        (new AutenticarUsuarioUseCase($lectura, $hasher, $tokens))->ejecutar('admin@gymcontrol.com', 'mala');
    }

    public function test_registrar_socio_rechaza_documento_duplicado(): void
    {
        $lectura = new class implements SocioLecturaPort {
            public function buscarPorId(int $id): ?Socio { return null; }
            public function buscarPorDocumento(string $documento): ?Socio {
                return new Socio(1, $documento, 'Existente');
            }
        };

        $escritura = new class implements SocioEscrituraPort {
            public function registrar(string $documento, string $nombre): Socio {
                return new Socio(2, $documento, $nombre);
            }
        };

        $this->expectException(SocioYaRegistradoException::class);

        (new RegistrarSocio($lectura, $escritura))->ejecutar('1001001001', 'Nuevo Socio');
    }

    public function test_tarifas_aplican_descuentos_configurables_del_puerto_de_ajustes(): void
    {
        $puertoAjustes = new class implements AjustesGimnasioPort {
            public function obtener(): AjustesGimnasio {
                return new AjustesGimnasio(
                    'Gym Custom',
                    PorcentajeDescuento::de(15),
                    PorcentajeDescuento::de(25),
                );
            }
            public function guardar(AjustesGimnasio $ajustes): AjustesGimnasio { return $ajustes; }
        };

        $trimestral = new TarifaTrimestral($puertoAjustes);
        $anual = new TarifaAnual($puertoAjustes);

        // 100.000 * 3 = 300.000 - 15% = 255.000
        $this->assertSame(255000, $trimestral->calcularPrecio(Dinero::de(100000))->monto());
        // 100.000 * 12 = 1.200.000 - 25% = 900.000
        $this->assertSame(900000, $anual->calcularPrecio(Dinero::de(100000))->monto());
    }
}
