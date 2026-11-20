<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class NotaController extends Controller
{
    public function index(): View
    {
        return view('notas.index');
    }

    public function store(): never
    {
        abort(501, 'Este endpoint está previsto para la siguiente entrega.');
    }
}
