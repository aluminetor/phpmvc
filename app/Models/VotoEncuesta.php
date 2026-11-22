<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotoEncuesta extends Model
{
    protected $table = 'votos_encuesta';

    protected $fillable = ['navegador_id', 'opcion_encuesta_id'];
}
