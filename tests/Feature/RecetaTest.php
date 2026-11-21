<?php

namespace Tests\Feature;

use App\Models\Receta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_publica_filtra_y_guarda_una_receta_sin_duplicarla(): void
    {
        $datos = [
            'titulo' => 'Arroz de la casa',
            'tipo' => 'Almuerzo',
            'ingredientes' => "Arroz integral 100%_!\nTomate",
            'preparacion' => "Lava el arroz.\nMezcla con el tomate.",
        ];
        $respuesta = $this->post(route('recetas.store'), $datos)->assertSessionHasNoErrors();
        $receta = Receta::query()->sole();
        $detalle = route('recetas.show', $receta);
        $respuesta->assertRedirect($detalle);

        Receta::create([...$datos, 'titulo' => 'Otra comida', 'tipo' => 'Cena']);
        Receta::create([...$datos, 'titulo' => 'Otra mezcla', 'ingredientes' => 'Arroz integral 100ab!']);

        $this->get($detalle)->assertOk()->assertSee('Tomate')->assertSee('Enlace para compartir');
        $this->get(route('recetas.index', ['tipo' => 'Almuerzo', 'ingrediente' => '100%_!']))
            ->assertOk()->assertSee('Arroz de la casa')->assertDontSee('Otra comida')->assertDontSee('Otra mezcla');

        $this->post(route('recetas.guardar', $receta))->assertRedirect($detalle);
        $this->post(route('recetas.guardar', $receta))->assertRedirect($detalle);
        $this->assertDatabaseCount('recetas_guardadas', 1);
        $guardadas = route('recetas.index', ['guardadas' => 1]);

        $this->get($guardadas)->assertOk()->assertSee('Arroz de la casa')->assertDontSee('Otra mezcla');
        $this->delete(route('recetas.quitar-guardado', $receta))->assertRedirect($detalle);
        $this->get($guardadas)->assertOk()->assertDontSee('Arroz de la casa');
        $this->assertDatabaseEmpty('recetas_guardadas');
    }
}
