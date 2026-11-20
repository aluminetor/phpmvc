<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MemoriaController extends Controller
{
    public function index(): View
    {
        return view('memoria.index');
    }

    public function iniciar(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function tablero(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function guardarPuntaje(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }
}
