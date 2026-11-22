<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpcionEncuesta extends Model
{
    protected $table = 'opciones_encuesta';

    protected $fillable = ['texto'];

    public function votos(): HasMany
    {
        return $this->hasMany(VotoEncuesta::class);
    }
}
