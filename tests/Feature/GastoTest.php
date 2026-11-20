<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GastoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_resumen_suma_por_categoria_y_respeta_los_limites_del_mes(): void
    {
        $gastos = [
            ['Almuerzo', 'Comida', '10,25', '2028-02-01'],
            ['Cena', 'Comida', '15.50', '2028-02-29'],
            ['Bus', 'Transporte', '2.10', '2028-02-29'],
            ['Gasto anterior', 'Fuera del mes', '90.00', '2028-01-31'],
            ['Gasto posterior', 'Fuera del mes', '90.00', '2028-03-01'],
        ];

        foreach ($gastos as [$concepto, $categoria, $monto, $fecha]) {
            $this->post(route('gastos.store'), compact('concepto', 'categoria', 'monto', 'fecha'))
                ->assertRedirect(route('gastos.index'))
                ->assertSessionHasNoErrors();
        }

        $this->get(route('gastos.resumen', ['mes' => '2028-02']))
            ->assertOk()
            ->assertSeeInOrder(['27,85', 'Comida', '25,75', 'Transporte', '2,10'])
            ->assertDontSee('Fuera del mes');
    }
}
