<?php

namespace Tests\Feature;

use App\Http\Middleware\IdentificarNavegador;
use App\Models\Encuesta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EncuestaTest extends TestCase
{
    use RefreshDatabase;

    public function test_registra_un_voto_por_navegador_y_calcula_los_resultados(): void
    {
        $this->get(route('encuestas.index'))->assertOk()->assertCookie(IdentificarNavegador::COOKIE);
        $this->withCookie(IdentificarNavegador::COOKIE, (string) Str::uuid());

        $respuesta = $this->post(route('encuestas.store'), [
            'pregunta' => '¿Qué fruta prefieres?',
            'opciones' => ['Mango', 'Papaya', '', 'Kiwi', '', '', '', ''],
        ])->assertSessionHasNoErrors();

        $encuesta = Encuesta::query()->sole();
        $respuesta->assertRedirect(route('encuestas.show', $encuesta));
        [$mango, $papaya] = $encuesta->opciones()->orderBy('id')->get();
        $this->assertSame(['Mango', 'Papaya', 'Kiwi'], $encuesta->opciones()->orderBy('id')->pluck('texto')->all());

        $votar = route('encuestas.votar', $encuesta);
        $resultados = route('encuestas.resultados', $encuesta);

        $this->get($resultados)->assertOk()->assertSee('Total de votos: 0')->assertSee('0,0 %');
        $this->post($votar, ['opcion_encuesta_id' => $mango->id])->assertRedirect($resultados);
        $this->post($votar, ['opcion_encuesta_id' => $papaya->id])
            ->assertRedirect($resultados)->assertSessionHas('estado', 'Este navegador ya registró un voto en esta encuesta.');

        $this->get(route('encuestas.show', $encuesta))->assertOk()->assertSee('Tu respuesta:')->assertSee('Mango');
        $this->assertDatabaseCount('votos_encuesta', 1);

        $this->withCookie(IdentificarNavegador::COOKIE, (string) Str::uuid())
            ->post($votar, ['opcion_encuesta_id' => $papaya->id])->assertRedirect($resultados);

        $this->assertDatabaseCount('votos_encuesta', 2);
        $this->get($resultados)->assertOk()->assertSeeInOrder(['Total de votos: 2', 'Mango', '1 voto', '50,0 %', 'Papaya', '1 voto', '50,0 %']);
    }

    public function test_rechaza_opciones_duplicadas_y_votos_por_opciones_de_otra_encuesta(): void
    {
        $this->post(route('encuestas.store'), ['pregunta' => 'Encuesta inválida', 'opciones' => ['Mango', ' mango ']])
            ->assertSessionHasErrors('opciones.1');
        $this->post(route('encuestas.store'), ['pregunta' => 'Encuesta incompleta', 'opciones' => ['', 'Papaya', 'Mango']])
            ->assertSessionHasErrors('opciones.0');
        $this->assertDatabaseEmpty('encuestas');

        $encuesta = Encuesta::create(['pregunta' => 'Encuesta local']);
        $otra = Encuesta::create(['pregunta' => 'Otra encuesta']);
        $opcion = $otra->opciones()->create(['texto' => 'Respuesta ajena']);

        $this->post(route('encuestas.votar', $encuesta), ['opcion_encuesta_id' => $opcion->id])
            ->assertSessionHasErrors('opcion_encuesta_id');

        $this->assertDatabaseEmpty('votos_encuesta');
    }
}
