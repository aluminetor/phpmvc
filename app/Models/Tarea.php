<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $fillable = ['titulo', 'completada'];

    protected function casts(): array
    {
        return ['completada' => 'boolean'];
    }
}
