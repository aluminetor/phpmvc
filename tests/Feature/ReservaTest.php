<?php

namespace Tests\Feature;

use App\Models\Reserva;
use App\Models\Servicio;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaTest extends TestCase
{
    use RefreshDatabase;

    public function test_una_cita_ocupa_el_horario_de_su_servicio_y_se_confirma_una_sola_vez(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-23 09:10:00', Servicio::ZONA_HORARIA));
        $servicio = Servicio::query()->orderBy('id')->firstOrFail();
        $otroServicio = Servicio::query()->whereKeyNot($servicio->id)->firstOrFail();
        $filtros = ['servicio_id' => $servicio->id, 'fecha' => '2026-11-23'];
        $disponibilidad = route('reservas.disponibilidad', $filtros);
        $datos = $filtros + ['hora' => '09:30', 'nombre' => 'Ana'];

        $this->get($disponibilidad)->assertOk()
            ->assertSee('value="09:30"', false)
            ->assertDontSee('value="09:00"', false);

        $this->post(route('reservas.store'), $datos)
            ->assertRedirect($disponibilidad)
            ->assertSessionHasNoErrors();

        $this->get($disponibilidad)->assertOk()
            ->assertDontSee('value="09:30"', false)
            ->assertSee('Pendiente');

        $this->get(route('reservas.disponibilidad', ['servicio_id' => $otroServicio->id, 'fecha' => $datos['fecha']]))
            ->assertSee('value="09:30"', false);

        $this->post(route('reservas.store'), $datos)
            ->assertRedirect($disponibilidad)
            ->assertSessionHasErrors(['hora' => 'El horario ya está reservado. Elige otro.']);

        $this->assertDatabaseCount('reservas', 1);
        $reserva = Reserva::query()->sole();

        $this->patch(route('reservas.confirmar', $reserva))
            ->assertRedirect(route('reservas.index'))
            ->assertSessionHas('estado', 'Reserva confirmada.');

        $confirmacion = $reserva->refresh()->confirmada_en->toDateTimeString();
        $this->assertSame('2026-11-23 14:10:00', $confirmacion);
        $this->travel(5)->minutes();
        $this->patch(route('reservas.confirmar', $reserva))->assertRedirect(route('reservas.index'));

        $this->assertSame($confirmacion, $reserva->refresh()->confirmada_en->toDateTimeString());
        $this->get(route('reservas.index'))->assertSee('Confirmada');
    }

    public function test_el_servidor_rechaza_horarios_pasados_o_fuera_del_horario_de_atencion(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-23 12:00:00', Servicio::ZONA_HORARIA));
        $servicio = Servicio::query()->firstOrFail();
        $casos = [
            ['fecha' => '2026-11-23', 'hora' => '11:30'],
            ['fecha' => '2026-11-23', 'hora' => '12:15'],
            ['fecha' => '2026-11-28', 'hora' => '12:30'],
        ];

        foreach ($casos as $caso) {
            $this->post(route('reservas.store'), $caso + ['servicio_id' => $servicio->id, 'nombre' => 'Ana'])
                ->assertSessionHasErrors('hora');
        }

        $this->assertDatabaseEmpty('reservas');
    }

    public function test_una_reserva_vencida_no_se_puede_confirmar(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-23 08:00:00', Servicio::ZONA_HORARIA));
        $servicio = Servicio::query()->firstOrFail();
        $this->post(route('reservas.store'), [
            'servicio_id' => $servicio->id,
            'fecha' => '2026-11-23',
            'hora' => '09:00',
            'nombre' => 'Ana',
        ])->assertSessionHasNoErrors();

        $this->travel(1)->hour();
        $reserva = Reserva::query()->sole();

        $this->patch(route('reservas.confirmar', $reserva))
            ->assertSessionHasErrors(['reserva' => 'La cita ya pasó.']);
        $this->assertNull($reserva->refresh()->confirmada_en);
        $this->get(route('reservas.index'))->assertSee('Vencida');
    }
}
