<?php

return [
    /*
    | Zona horaria con la que el dominio evalúa horarios de acceso y vigencias.
    */
    'zona_horaria' => env('GYM_ZONA_HORARIA', 'America/Bogota'),

    /*
    | Hora fija opcional para demos (p. ej. "2026-11-17 23:30"). Dejar vacío en producción.
    */
    'hora_simulada' => env('GYM_HORA_SIMULADA'),
];
