<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Receta extends Model
{
    protected $fillable = ['titulo', 'tipo', 'ingredientes', 'preparacion'];

    public function guardado(): HasOne
    {
        return $this->hasOne(RecetaGuardada::class);
    }

    public function scopeConIngrediente(Builder $query, string $ingrediente): Builder
    {
        $patron = '%'.strtr($ingrediente, ['!' => '!!', '%' => '!%', '_' => '!_']).'%';

        return $query->whereRaw("ingredientes LIKE ? ESCAPE '!'", [$patron]);
    }
}
