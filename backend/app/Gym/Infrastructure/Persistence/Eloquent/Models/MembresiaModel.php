<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembresiaModel extends Model
{
    protected $table = 'membresias';

    protected $fillable = [
        'socio_id',
        'plan_id',
        'tipo_tarifa',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'precio_final',
        'fecha_congelamiento',
        'dias_congelamiento',
        'dias_congelados_acumulados',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_fin' => 'date:Y-m-d',
        'fecha_congelamiento' => 'date:Y-m-d',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanModel::class, 'plan_id');
    }
}
