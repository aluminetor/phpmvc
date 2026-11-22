<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vuelta extends Model
{
    protected $fillable = ['numero', 'parcial_ms', 'total_ms'];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'parcial_ms' => 'integer',
            'total_ms' => 'integer',
        ];
    }
}
