<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroAccesoModel extends Model
{
    protected $table = 'registros_acceso';

    protected $fillable = ['socio_id', 'membresia_id', 'fecha_hora', 'permitido', 'motivo'];

    protected $casts = ['permitido' => 'boolean'];

    public function socio(): BelongsTo
    {
        return $this->belongsTo(SocioModel::class, 'socio_id');
    }

    public function membresia(): BelongsTo
    {
        return $this->belongsTo(MembresiaModel::class, 'membresia_id');
    }
}
