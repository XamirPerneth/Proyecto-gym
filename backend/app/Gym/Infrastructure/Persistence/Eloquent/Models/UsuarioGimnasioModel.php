<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioGimnasioModel extends Model
{
    protected $table = 'usuarios_gimnasio';

    protected $fillable = ['nombre', 'email', 'rol', 'password'];
}
