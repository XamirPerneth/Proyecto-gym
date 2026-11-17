<?php

namespace Database\Seeders;

use App\Gym\Infrastructure\Persistence\Eloquent\Models\AjustesGimnasioModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\MembresiaModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\PlanModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\RegistroAccesoModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\SocioModel;
use App\Gym\Infrastructure\Persistence\Eloquent\Models\UsuarioGimnasioModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GymSeeder extends Seeder
{
    public function run(): void
    {
        UsuarioGimnasioModel::create([
            'nombre' => 'Valentina Rojas',
            'email' => 'admin@gymcontrol.com',
            'rol' => 'Administrador General',
            'password' => Hash::make('admin123'),
        ]);

        AjustesGimnasioModel::create([
            'nombre_gimnasio' => 'Gym Control Club',
            'descuento_trimestral' => 10,
            'descuento_anual' => 20,
            'hora_simulada' => null,
        ]);

        $basico = PlanModel::create([
            'nombre' => 'Básico',
            'hora_inicio' => '06:00',
            'hora_fin' => '16:00',
            'precio_base_mensual' => 80000,
        ]);

        $full = PlanModel::create([
            'nombre' => 'Full',
            'hora_inicio' => '05:00',
            'hora_fin' => '23:00',
            'precio_base_mensual' => 130000,
        ]);

        // 1. Tres socios base intactos (para botones rápidos del torniquete y pruebas)
        [$carlos, $memCarlos] = $this->crearMembresia('1001001001', 'Carlos Ramírez', $full, 'MENSUAL', 'ACTIVA', -5, 25, 130000);
        [$laura, $memLaura] = $this->crearMembresia('1002002002', 'Laura Gómez', $basico, 'TRIMESTRAL', 'ACTIVA', -10, 80, 216000);
        [$pedro, $memPedro] = $this->crearMembresia('1003003003', 'Pedro Martínez', $basico, 'MENSUAL', 'VENCIDA', -60, -30, 80000);

        // 2. Quince socios adicionales (documentos 1004004004 al 1018018018)
        // 2a. 7 Socios con membresías ACTIVAS variadas (Full/Básico, mensual/trimestral/anual)
        [$andres, $memAndres] = $this->crearMembresia('1004004004', 'Andrés Felipe Castro', $full, 'ANUAL', 'ACTIVA', -45, 320, 1248000);
        [$camila, $memCamila] = $this->crearMembresia('1005005005', 'Camila Andrea Vargas', $full, 'TRIMESTRAL', 'ACTIVA', -20, 70, 351000);
        [$santiago, $memSantiago] = $this->crearMembresia('1006006006', 'Santiago Morales', $basico, 'MENSUAL', 'ACTIVA', -12, 18, 80000);
        [$daniela, $memDaniela] = $this->crearMembresia('1007007007', 'Daniela Ospina', $full, 'MENSUAL', 'ACTIVA', -3, 27, 130000);
        [$mateo, $memMateo] = $this->crearMembresia('1008008008', 'Mateo Restrepo', $basico, 'ANUAL', 'ACTIVA', -100, 265, 768000);
        [$natalia, $memNatalia] = $this->crearMembresia('1009009009', 'Natalia Jiménez', $full, 'TRIMESTRAL', 'ACTIVA', -35, 55, 351000);
        [$sebastian, $memSebastian] = $this->crearMembresia('1010010010', 'Sebastián Herrera', $basico, 'MENSUAL', 'ACTIVA', -22, 8, 80000);

        // 2b. 3 Socios con membresías CONGELADAS actualmente
        [$juliana, $memJuliana] = $this->crearMembresia('1011011011', 'Juliana Ríos', $full, 'TRIMESTRAL', 'CONGELADA', -25, 65, 351000, -4, 15);
        [$alejandro, $memAlejandro] = $this->crearMembresia('1012012012', 'Alejandro Mendoza', $basico, 'MENSUAL', 'CONGELADA', -14, 16, 80000, -2, 7);
        [$paula, $memPaula] = $this->crearMembresia('1013013013', 'Paula Andrea Salazar', $full, 'ANUAL', 'CONGELADA', -90, 275, 1248000, -6, 30);

        // 2c. 3 Socios con membresías VENCIDAS hace pocos días
        [$jorge, $memJorge] = $this->crearMembresia('1014014014', 'Jorge Iván Patiño', $full, 'MENSUAL', 'VENCIDA', -34, -4, 130000);
        [$carolina, $memCarolina] = $this->crearMembresia('1015015015', 'Carolina Mejía', $basico, 'MENSUAL', 'VENCIDA', -32, -2, 80000);
        [$diego, $memDiego] = $this->crearMembresia('1016016016', 'Diego Fernando Quintero', $basico, 'TRIMESTRAL', 'VENCIDA', -95, -5, 216000);

        // 2d. 2 Socios recién registrados SIN membresía asignada
        SocioModel::create(['documento' => '1017017017', 'nombre' => 'Mariana Alejandra Cano']);
        SocioModel::create(['documento' => '1018018018', 'nombre' => 'Esteban Giraldo']);

        // 3. 25 registros históricos en registros_acceso (ayer y hoy en distintas franjas)
        $ayer = now()->subDay()->format('Y-m-d');
        $hoy = now()->format('Y-m-d');

        $accesos = [
            // Ayer (6 registros)
            [$carlos, $memCarlos, "{$ayer} 06:12:18", true, null],
            [$laura, $memLaura, "{$ayer} 07:40:05", true, null],
            [$andres, $memAndres, "{$ayer} 11:20:44", true, null],
            [$jorge, $memJorge, "{$ayer} 14:10:30", false, 'Acceso denegado: la membresía se encuentra VENCIDA.'],
            [$camila, $memCamila, "{$ayer} 18:05:12", true, null],
            [$santiago, $memSantiago, "{$ayer} 19:15:50", false, 'Acceso denegado: el plan Básico solo permite el ingreso entre 06:00 y 16:00 y son las 19:15.'],

            // Hoy en distintas franjas horarias (19 registros -> total 25)
            [$carlos, $memCarlos, "{$hoy} 06:05:10", true, null],
            [$mateo, $memMateo, "{$hoy} 06:28:42", true, null],
            [$santiago, $memSantiago, "{$hoy} 07:15:09", true, null],
            [$laura, $memLaura, "{$hoy} 08:10:22", true, null],
            [$daniela, $memDaniela, "{$hoy} 08:45:19", true, null],
            [$sebastian, $memSebastian, "{$hoy} 09:20:33", true, null],
            [$pedro, $memPedro, "{$hoy} 09:35:05", false, 'Acceso denegado: la membresía se encuentra VENCIDA.'],
            [$andres, $memAndres, "{$hoy} 10:12:48", true, null],
            [$camila, $memCamila, "{$hoy} 11:05:14", true, null],
            [$juliana, $memJuliana, "{$hoy} 11:40:02", false, 'Acceso denegado: la membresía se encuentra CONGELADA.'],
            [$natalia, $memNatalia, "{$hoy} 12:30:27", true, null],
            [$carolina, $memCarolina, "{$hoy} 13:15:40", false, 'Acceso denegado: la membresía se encuentra VENCIDA.'],
            [$mateo, $memMateo, "{$hoy} 14:22:11", true, null],
            [$santiago, $memSantiago, "{$hoy} 15:10:55", true, null],
            [$carlos, $memCarlos, "{$hoy} 16:40:18", true, null],
            [$laura, $memLaura, "{$hoy} 17:25:00", false, 'Acceso denegado: el plan Básico solo permite el ingreso entre 06:00 y 16:00 y son las 17:25.'],
            [$daniela, $memDaniela, "{$hoy} 18:15:36", true, null],
            [$andres, $memAndres, "{$hoy} 19:05:08", true, null],
            [$camila, $memCamila, "{$hoy} 20:20:45", true, null],
        ];

        foreach ($accesos as [$socio, $membresia, $fechaHora, $permitido, $motivo]) {
            RegistroAccesoModel::create([
                'socio_id' => $socio->id,
                'membresia_id' => $membresia->id,
                'fecha_hora' => $fechaHora,
                'permitido' => $permitido,
                'motivo' => $motivo,
            ]);
        }
    }

    /** @return array{0: SocioModel, 1: MembresiaModel} */
    private function crearMembresia(
        string $documento,
        string $nombre,
        PlanModel $plan,
        string $tarifa,
        string $estado,
        int $inicioDesdeHoy,
        int $finDesdeHoy,
        int $precio,
        ?int $congeladoDesdeHoy = null,
        int $diasCongelamiento = 0,
    ): array {
        $socio = SocioModel::create(['documento' => $documento, 'nombre' => $nombre]);

        $membresia = MembresiaModel::create([
            'socio_id' => $socio->id,
            'plan_id' => $plan->id,
            'tipo_tarifa' => $tarifa,
            'estado' => $estado,
            'fecha_inicio' => now()->addDays($inicioDesdeHoy)->toDateString(),
            'fecha_fin' => now()->addDays($finDesdeHoy)->toDateString(),
            'precio_final' => $precio,
            'fecha_congelamiento' => $congeladoDesdeHoy !== null ? now()->addDays($congeladoDesdeHoy)->toDateString() : null,
            'dias_congelamiento' => $diasCongelamiento,
        ]);

        return [$socio, $membresia];
    }
}
