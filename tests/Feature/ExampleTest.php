<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_la_pagina_de_inicio_redirige_a_la_lista_de_tareas(): void
    {
        $this->get('/')->assertRedirect(route('tareas.index'));
    }
}
