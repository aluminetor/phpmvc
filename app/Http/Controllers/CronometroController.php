<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CronometroController extends Controller
{
    public function index(): View
    {
        return view('cronometro.index');
    }

    public function iniciar(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function pausar(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function reiniciar(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function guardarVuelta(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }
}
