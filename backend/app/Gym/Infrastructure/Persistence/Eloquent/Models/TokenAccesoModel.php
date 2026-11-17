<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenAccesoModel extends Model
{
    protected $table = 'tokens_acceso';

    protected $fillable = ['usuario_id', 'hash_token'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UsuarioGimnasioModel::class, 'usuario_id');
    }
}
