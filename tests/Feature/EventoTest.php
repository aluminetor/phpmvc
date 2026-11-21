<?php

namespace Tests\Feature;

use App\Models\Evento;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_calendario_usa_hora_local_para_el_mes_y_los_recordatorios(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-30 23:00:00', Evento::ZONA_HORARIA));

        $datos = [
            'titulo' => 'Cierre mensual',
            'descripcion' => 'Revisar pendientes',
            'inicio_en' => '2026-11-30T23:40',
            'recordatorio_minutos' => 60,
        ];

        $noviembre = route('eventos.index', ['mes' => '2026-11']);
        $this->post(route('eventos.store'), $datos)
            ->assertRedirect($noviembre)
            ->assertSessionHasNoErrors();

        $evento = Evento::query()->sole();
        $this->assertSame('2026-12-01 04:40:00', $evento->inicio_en->toDateTimeString());

        $this->get($noviembre)->assertOk()
            ->assertSee('noviembre de 2026')
            ->assertSee('Recordatorios activos')
            ->assertViewHas('porDia', fn ($dias) => $dias->get(30)->sole()->is($evento));

        $this->get(route('eventos.edit', $evento))
            ->assertOk()
            ->assertSee('value="2026-11-30T23:40"', false);

        $diciembre = route('eventos.index', ['mes' => '2026-12']);
        $this->put(route('eventos.update', $evento), [
            ...$datos,
            'titulo' => 'Plan mensual',
            'inicio_en' => '2026-12-01T00:30',
            'recordatorio_minutos' => null,
        ])->assertRedirect($diciembre);

        $this->get($noviembre)->assertOk()->assertDontSee('Cierre mensual')->assertDontSee('Recordatorios activos');
        $this->get($diciembre)->assertOk()->assertSee('Plan mensual');
        $this->delete(route('eventos.destroy', $evento))->assertRedirect($diciembre);
        $this->assertDatabaseEmpty('eventos');
    }
}
