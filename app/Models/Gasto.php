<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Gasto extends Model
{
    protected $fillable = ['concepto', 'categoria', 'monto', 'fecha'];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha' => 'immutable_date',
        ];
    }

    protected function fecha(): Attribute
    {
        return Attribute::set(fn ($fecha): string => $this->asDate($fecha)->toDateString());
    }

    public static function resumenMensual(CarbonImmutable $mes): Collection
    {
        return self::query()
            ->whereBetween('fecha', [$mes->startOfMonth()->toDateString(), $mes->endOfMonth()->toDateString()])
            ->selectRaw('categoria, COUNT(*) AS cantidad, SUM(monto) AS subtotal, SUM(SUM(monto)) OVER () AS total')
            ->groupBy('categoria')
            ->orderByDesc('subtotal')
            ->orderBy('categoria')
            ->toBase()
            ->get();
    }
}
