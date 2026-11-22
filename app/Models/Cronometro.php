<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cronometro extends Model
{
    protected $fillable = ['navegador_id'];

    protected $attributes = [
        'acumulado_ms' => 0,
        'version' => 0,
    ];

    protected function casts(): array
    {
        return [
            'acumulado_ms' => 'integer',
            'iniciado_en_ms' => 'integer',
            'version' => 'integer',
        ];
    }

    public function vueltas(): HasMany
    {
        return $this->hasMany(Vuelta::class);
    }

    public function enMarcha(): bool
    {
        return $this->iniciado_en_ms !== null;
    }

    public function transcurrido(int $ahora): int
    {
        $intervalo = $this->enMarcha() ? max(0, $ahora - $this->iniciado_en_ms) : 0;

        return $this->acumulado_ms + $intervalo;
    }

    public function iniciar(int $ahora): void
    {
        $this->iniciado_en_ms ??= $ahora;
    }

    public function pausar(int $ahora): void
    {
        $this->acumulado_ms = $this->transcurrido($ahora);
        $this->iniciado_en_ms = null;
    }

    public function reiniciar(): void
    {
        $this->vueltas()->delete();
        $this->acumulado_ms = 0;
        $this->iniciado_en_ms = null;
    }

    public function registrarVuelta(int $ahora): void
    {
        $anterior = $this->vueltas()->latest('numero')->first();
        $acumuladoAnterior = $anterior?->total_ms ?? 0;
        $total = max($acumuladoAnterior, $this->transcurrido($ahora));

        $this->vueltas()->create([
            'numero' => ($anterior?->numero ?? 0) + 1,
            'parcial_ms' => $total - $acumuladoAnterior,
            'total_ms' => $total,
        ]);
    }

    public static function formatear(int $milisegundos): string
    {
        $segundos = intdiv($milisegundos, 1000);

        return sprintf(
            '%02d:%02d:%02d.%03d',
            intdiv($segundos, 3600),
            intdiv($segundos % 3600, 60),
            $segundos % 60,
            $milisegundos % 1000,
        );
    }
}
