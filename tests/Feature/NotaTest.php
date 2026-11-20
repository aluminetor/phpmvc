<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_busqueda_combina_categoria_y_palabra_clave_literal_en_titulo_o_contenido(): void
    {
        $notas = [
            ['Avance 100%_!', 'Estado del proyecto', 'Trabajo'],
            ['Seguimiento', 'Confirmado el avance 100%_!', 'Trabajo'],
            ['Avance 100ab!', 'El patrón es distinto', 'Trabajo'],
            ['Proyecto personal', 'Terminado al 100%_!', 'Personal'],
        ];

        foreach ($notas as [$titulo, $contenido, $categoria]) {
            $this->post(route('notas.store'), compact('titulo', 'contenido', 'categoria'))
                ->assertRedirect(route('notas.index'))
                ->assertSessionHasNoErrors();
        }

        $this->get(route('notas.index', ['q' => '100%_!', 'categoria' => 'Trabajo']))
            ->assertOk()
            ->assertSee('Avance 100%_!')
            ->assertSee('Seguimiento')
            ->assertDontSee('Avance 100ab!')
            ->assertDontSee('Proyecto personal');
    }
}
