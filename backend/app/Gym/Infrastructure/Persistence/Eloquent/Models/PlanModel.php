<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class PlanModel extends Model
{
    protected $table = 'planes';

    protected $fillable = ['nombre', 'hora_inicio', 'hora_fin', 'precio_base_mensual'];
}
