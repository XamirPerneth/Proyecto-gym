<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class AjustesGimnasioModel extends Model
{
    protected $table = 'ajustes_gimnasio';

    protected $fillable = [
        'nombre_gimnasio',
        'descuento_trimestral',
        'descuento_anual',
        'hora_simulada',
    ];
}
