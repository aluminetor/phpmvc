<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EncuestaController extends Controller
{
    public function index(): View
    {
        return view('encuestas.index');
    }

    public function store(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function show(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function votar(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function resultados(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }
}
