<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    protected $fillable = ['nombre', 'fecha', 'hora'];

    protected function casts(): array
    {
        return [
            'fecha' => 'immutable_date',
            'confirmada_en' => 'immutable_datetime',
        ];
    }

    protected function fecha(): Attribute
    {
        return Attribute::set(fn ($fecha): string => $this->asDate($fecha)->toDateString());
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function inicio(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->fecha->toDateString().' '.$this->hora, Servicio::ZONA_HORARIA);
    }
}
