<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class GastoController extends Controller
{
    public function index(): View
    {
        return view('gastos.index');
    }

    public function store(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function resumen(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }
}
