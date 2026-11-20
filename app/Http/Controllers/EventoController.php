<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EventoController extends Controller
{
    public function index(): View
    {
        return view('eventos.index');
    }

    public function store(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function edit(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function update(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function destroy(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }
}
