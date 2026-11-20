<?php

namespace App\Http\Controllers;

use App\Models\GeneradorContrasenas;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContrasenaController extends Controller
{
    public function index(): View
    {
        return view('contrasenas.index');
    }

    public function generar(Request $request, GeneradorContrasenas $generador): Response
    {
        $datos = $request->validate([
            'longitud' => ['required', 'integer', 'between:12,128'],
            'tipos' => ['required', 'array', 'min:1', 'max:4'],
            'tipos.*' => ['required', 'string', 'distinct', Rule::in(array_keys(GeneradorContrasenas::TIPOS))],
        ], [
            'longitud.*' => 'Indica una longitud de 12 a 128 caracteres.',
            'tipos.*' => 'Selecciona al menos un tipo de carácter válido, sin repetir opciones.',
            'tipos.*.*' => 'Selecciona únicamente los tipos de carácter disponibles.',
        ]);

        return response()
            ->view('contrasenas.index', [
                'datos' => $datos,
                'contrasena' => $generador->generar((int) $datos['longitud'], $datos['tipos']),
            ])
            ->header('Cache-Control', 'no-store, private');
    }
}
