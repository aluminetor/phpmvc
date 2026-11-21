<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class PartidaMemoria extends Model
{
    public const NIVELES = [
        'facil' => ['nombre' => 'Fácil', 'pares' => 4],
        'media' => ['nombre' => 'Media', 'pares' => 6],
        'dificil' => ['nombre' => 'Difícil', 'pares' => 8],
    ];

    private const FIGURAS = [
        ['🍎', 'Manzana'],
        ['🦋', 'Mariposa'],
        ['🚲', 'Bicicleta'],
        ['🌻', 'Girasol'],
        ['🐢', 'Tortuga'],
        ['🎈', 'Globo'],
        ['🌙', 'Luna'],
        ['🐙', 'Pulpo'],
    ];

    protected $table = 'partidas_memoria';

    protected $fillable = ['uuid', 'dificultad', 'mazo', 'seleccion', 'encontradas', 'intentos', 'turno', 'terminada_en'];

    protected $attributes = [
        'seleccion' => '[]',
        'encontradas' => '[]',
        'intentos' => 0,
        'turno' => 0,
    ];

    protected function casts(): array
    {
        return [
            'mazo' => 'array',
            'seleccion' => 'array',
            'encontradas' => 'array',
            'intentos' => 'integer',
            'turno' => 'integer',
            'terminada_en' => 'immutable_datetime',
        ];
    }

    public static function nueva(string $dificultad): self
    {
        $figuras = range(0, self::NIVELES[$dificultad]['pares'] - 1);
        $mazo = array_merge($figuras, $figuras);
        shuffle($mazo);

        return self::create([
            'uuid' => Str::uuid()->toString(),
            'dificultad' => $dificultad,
            'mazo' => $mazo,
        ]);
    }

    public function puntaje(): HasOne
    {
        return $this->hasOne(PuntajeMemoria::class, 'partida_id');
    }

    public function cartasVisibles(): array
    {
        $visibles = array_merge($this->encontradas, $this->seleccion);
        $cartas = [];

        foreach ($this->mazo as $posicion => $figura) {
            $cartas[] = in_array($posicion, $visibles, true) ? self::FIGURAS[$figura] : null;
        }

        return $cartas;
    }

    public function voltear(int $posicion, int $turno): bool
    {
        if ($this->terminada_en !== null || $this->turno !== $turno
            || ! isset($this->mazo[$posicion]) || in_array($posicion, $this->encontradas, true)) {
            return false;
        }

        $seleccion = count($this->seleccion) === 2 ? [] : $this->seleccion;

        if (in_array($posicion, $seleccion, true)) {
            return false;
        }

        $seleccion[] = $posicion;

        if (count($seleccion) === 2) {
            $this->intentos++;
            [$primera, $segunda] = $seleccion;

            if ($this->mazo[$primera] === $this->mazo[$segunda]) {
                $this->encontradas = array_merge($this->encontradas, $seleccion);
                $seleccion = [];

                if (count($this->encontradas) === count($this->mazo)) {
                    $this->terminada_en = now();
                }
            }
        }

        $this->seleccion = $seleccion;
        $this->turno++;

        return $this->save();
    }

    public function segundosJugados(): int
    {
        return max(0, (int) $this->created_at->diffInSeconds($this->terminada_en));
    }
}
