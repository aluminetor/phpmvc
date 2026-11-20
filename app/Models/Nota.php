<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = ['titulo', 'contenido', 'categoria'];

    public function scopeBuscar(Builder $query, string $palabra): Builder
    {
        $patron = '%'.strtr($palabra, ['!' => '!!', '%' => '!%', '_' => '!_']).'%';

        return $query->where(function (Builder $notas) use ($patron): void {
            $notas->whereRaw("titulo LIKE ? ESCAPE '!'", [$patron])
                ->orWhereRaw("contenido LIKE ? ESCAPE '!'", [$patron]);
        });
    }
}
