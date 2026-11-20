<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class RecetaController extends Controller
{
    public function index(): View
    {
        return view('recetas.index');
    }

    public function store(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function show(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }

    public function guardar(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }
}
