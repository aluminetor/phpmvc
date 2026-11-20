<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ReservaController extends Controller
{
    public function index(): View
    {
        return view('reservas.index');
    }

    public function disponibilidad(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function store(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function confirmar(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }
}
