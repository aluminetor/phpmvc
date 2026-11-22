<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encuesta extends Model
{
    protected $fillable = ['pregunta'];

    public function opciones(): HasMany
    {
        return $this->hasMany(OpcionEncuesta::class);
    }

    public function votos(): HasMany
    {
        return $this->hasMany(VotoEncuesta::class);
    }
}
