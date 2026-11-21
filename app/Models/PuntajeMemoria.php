<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntajeMemoria extends Model
{
    protected $table = 'puntajes_memoria';

    protected $fillable = ['nombre', 'dificultad', 'intentos', 'segundos'];
}
