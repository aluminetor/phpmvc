<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaGuardada extends Model
{
    protected $table = 'recetas_guardadas';

    protected $fillable = ['receta_id'];
}
