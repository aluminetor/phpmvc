<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    public const ZONA_HORARIA = 'America/Bogota';

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function horariosAgendables(CarbonImmutable $fecha, CarbonImmutable $ahora): array
    {
        if ($fecha->isWeekend()) {
            return [];
        }

        $horarios = [];
        $cierre = $fecha->setTime(17, 0);

        for ($hora = $fecha->setTime(9, 0); $hora->lessThan($cierre); $hora = $hora->addMinutes(30)) {
            if ($hora->greaterThan($ahora)) {
                $horarios[] = $hora->format('H:i');
            }
        }

        return $horarios;
    }

    public function horariosDisponibles(CarbonImmutable $fecha, CarbonImmutable $ahora): array
    {
        $ocupados = $this->reservas()->where('fecha', $fecha->toDateString())->pluck('hora')
            ->map(fn (string $hora): string => substr($hora, 0, 5));

        return array_values(array_diff($this->horariosAgendables($fecha, $ahora), $ocupados->all()));
    }
}
