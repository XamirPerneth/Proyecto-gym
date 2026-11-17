<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocioModel extends Model
{
    protected $table = 'socios';

    protected $fillable = ['documento', 'nombre'];

    public function membresias(): HasMany
    {
        return $this->hasMany(MembresiaModel::class, 'socio_id');
    }
}
