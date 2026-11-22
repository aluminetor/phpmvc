<?php

namespace Tests\Feature;

use App\Http\Middleware\IdentificarNavegador;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CronometroTest extends TestCase
{
    use RefreshDatabase;

    public function test_pausa_reanuda_y_registra_vueltas_sin_contar_el_tiempo_pausado(): void
    {
        $this->withCookie(IdentificarNavegador::COOKIE, (string) Str::uuid());
        $inicio = CarbonImmutable::parse('2026-11-22 12:00:00', 'UTC');
        $this->travelTo($inicio);
        $pagina = route('cronometro.index');
        $vuelta = route('cronometro.vueltas.store');

        $this->get($pagina)->assertOk()->assertSee('00:00:00.000');
        $this->post(route('cronometro.iniciar'), ['version' => 0])->assertRedirect($pagina)->assertSessionHasNoErrors();

        $this->travelTo($inicio->addMilliseconds(1250));
        $this->post($vuelta, ['version' => 1, 'total_ms' => 9])->assertRedirect($pagina)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vueltas', ['numero' => 1, 'parcial_ms' => 1250, 'total_ms' => 1250]);

        $this->post($vuelta, ['version' => 1])->assertSessionHasErrors('cronometro');
        $this->assertDatabaseCount('vueltas', 1);

        $this->travelTo($inicio->addMilliseconds(2500));
        $this->patch(route('cronometro.pausar'), ['version' => 2])->assertRedirect($pagina)->assertSessionHasNoErrors();
        $this->travelTo($inicio->addSeconds(10));
        $this->get($pagina)->assertOk()->assertSee('00:00:02.500')->assertSee('Reanudar');
        $this->post($vuelta, ['version' => 3])->assertSessionHasErrors('cronometro');

        $this->post(route('cronometro.iniciar'), ['version' => 3])->assertRedirect($pagina)->assertSessionHasNoErrors();
        $this->travelTo($inicio->addMilliseconds(11250));
        $this->post($vuelta, ['version' => 4])->assertRedirect($pagina)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vueltas', ['numero' => 2, 'parcial_ms' => 2500, 'total_ms' => 3750]);

        $this->delete(route('cronometro.reiniciar'), ['version' => 5])->assertRedirect($pagina)->assertSessionHasNoErrors();
        $this->assertDatabaseEmpty('vueltas');
        $this->get($pagina)->assertOk()->assertSee('00:00:00.000')->assertSee('Detenido');
    }

    public function test_el_cronometro_y_sus_vueltas_estan_separados_por_navegador(): void
    {
        $pagina = route('cronometro.index');
        $primero = (string) Str::uuid();

        $this->withCookie(IdentificarNavegador::COOKIE, $primero)
            ->post(route('cronometro.iniciar'), ['version' => 0])->assertRedirect($pagina);
        $this->post(route('cronometro.vueltas.store'), ['version' => 1])->assertRedirect($pagina)->assertSessionHasNoErrors();

        $this->withCookie(IdentificarNavegador::COOKIE, (string) Str::uuid())
            ->get($pagina)->assertOk()->assertSee('Aún no hay vueltas registradas.');
        $this->delete(route('cronometro.reiniciar'), ['version' => 0])->assertRedirect($pagina);
        $this->assertDatabaseCount('vueltas', 1);

        $this->withCookie(IdentificarNavegador::COOKIE, $primero)
            ->get($pagina)->assertOk()->assertSee('En marcha')->assertSee('Tiempo de vuelta');
    }
}
