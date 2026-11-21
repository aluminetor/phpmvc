<?php

namespace Tests\Feature;

use App\Models\PartidaMemoria;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_servidor_valida_los_turnos_y_guarda_un_solo_puntaje_calculado(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-21 12:00:00', 'UTC'));

        $tablero = route('memoria.tablero');
        $voltear = route('memoria.voltear');
        $puntajes = route('memoria.puntajes.store');

        $this->post(route('memoria.iniciar'), ['dificultad' => 'facil'])->assertRedirect($tablero);
        $partida = PartidaMemoria::query()->sole();
        $partida->update(['mazo' => [0, 1, 0, 1, 2, 2, 3, 3]]);
        $uuid = $partida->uuid;

        $this->get($tablero)->assertOk()->assertSee('Carta 1: oculta')->assertDontSee('Manzana');
        $this->post($puntajes, ['partida' => $uuid, 'nombre' => 'Alicia'])->assertSessionHasErrors('partida');
        $this->assertDatabaseEmpty('puntajes_memoria');

        $this->post($voltear, ['partida' => $uuid, 'posicion' => 0, 'turno' => 0])->assertRedirect($tablero);
        $this->get($tablero)->assertOk()->assertSee('Carta 1: Manzana')->assertSee('Carta 2: oculta');
        $this->post($voltear, ['partida' => $uuid, 'posicion' => 1, 'turno' => 0])->assertSessionHasErrors('posicion');
        $this->post($voltear, ['partida' => $uuid, 'posicion' => 0, 'turno' => 1])->assertSessionHasErrors('posicion');
        $this->post($voltear, ['partida' => $uuid, 'posicion' => 1, 'turno' => 1])->assertRedirect($tablero);
        $this->get($tablero)->assertOk()->assertSee('Las cartas son distintas.');

        $this->travel(37)->seconds();
        foreach ([0, 2, 1, 3, 4, 5, 6, 7] as $paso => $posicion) {
            $this->post($voltear, ['partida' => $uuid, 'posicion' => $posicion, 'turno' => $paso + 2])
                ->assertRedirect($tablero)
                ->assertSessionHasNoErrors();
        }

        $this->get($tablero)->assertOk()->assertSee('Partida completada en 37 s.');
        $clasificacion = route('memoria.index', ['dificultad' => 'facil']);
        $this->post($puntajes, ['partida' => $uuid, 'nombre' => 'Alicia', 'intentos' => 1, 'segundos' => 1])
            ->assertRedirect($clasificacion);
        $this->post($puntajes, ['partida' => $uuid, 'nombre' => 'Otro nombre'])->assertRedirect($clasificacion);

        $this->assertDatabaseCount('puntajes_memoria', 1);
        $this->assertDatabaseHas('puntajes_memoria', [
            'nombre' => 'Alicia',
            'intentos' => 5,
            'segundos' => 37,
        ]);

        $this->get($clasificacion)->assertOk()->assertSee('Alicia');
        $this->get(route('memoria.index', ['dificultad' => 'media']))->assertOk()->assertDontSee('Alicia');
    }

    public function test_cada_nivel_configura_sus_pares_y_una_partida_nueva_invalida_las_jugadas_anteriores(): void
    {
        foreach (['facil' => 4, 'media' => 6, 'dificil' => 8] as $dificultad => $pares) {
            $this->post(route('memoria.iniciar'), ['dificultad' => $dificultad])->assertRedirect(route('memoria.tablero'));
            $partida = PartidaMemoria::query()->latest('id')->firstOrFail();

            $this->assertCount($pares * 2, $partida->mazo);
            $this->assertSame(array_fill(0, $pares, 2), array_values(array_count_values($partida->mazo)));
        }

        $anterior = PartidaMemoria::query()->oldest('id')->firstOrFail();
        $this->post(route('memoria.voltear'), ['partida' => $anterior->uuid, 'posicion' => 0, 'turno' => 0])
            ->assertSessionHasErrors('partida');
        $this->assertSame(0, $anterior->refresh()->turno);
    }
}
