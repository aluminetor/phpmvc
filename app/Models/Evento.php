<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    public const ZONA_HORARIA = 'America/Bogota';

    public const RECORDATORIOS = [
        15 => '15 minutos antes',
        30 => '30 minutos antes',
        60 => '1 hora antes',
        1440 => '1 día antes',
        10080 => '1 semana antes',
    ];

    protected $fillable = ['titulo', 'descripcion', 'inicio_en', 'recordatorio_minutos'];

    protected function casts(): array
    {
        return [
            'inicio_en' => 'immutable_datetime',
            'recordatorio_minutos' => 'integer',
        ];
    }

    public static function delMes(CarbonImmutable $mes): Collection
    {
        return self::query()->where('inicio_en', '>=', $mes->utc())
            ->where('inicio_en', '<=', $mes->endOfMonth()->utc()->min('9999-12-31 23:59:59'))
            ->orderBy('inicio_en')->orderBy('id')->get();
    }

    public static function conRecordatorioActivo(CarbonImmutable $ahora): Collection
    {
        return self::query()->whereNotNull('recordatorio_minutos')
            ->whereBetween('inicio_en', [$ahora->utc(), $ahora->addWeek()->utc()])
            ->orderBy('inicio_en')->get()
            ->filter(fn (Evento $evento): bool => $evento->inicio_en->subMinutes($evento->recordatorio_minutos) <= $ahora);
    }

    public function inicioLocal(): CarbonImmutable
    {
        return $this->inicio_en->setTimezone(self::ZONA_HORARIA);
    }
}
