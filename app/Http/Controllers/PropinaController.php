<?php

namespace App\Http\Controllers;

use App\Models\CalculoPropina;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropinaController extends Controller
{
    public function index(): View
    {
        return view('propinas.index');
    }

    public function calcular(Request $request): View
    {
        $datos = $request->validate([
            'monto' => ['bail', 'required', 'numeric', 'min:0.01', 'max:999999999.99', 'regex:/^(?:\d{1,9}|\d{0,9}\.\d{1,2})$/D'],
            'porcentaje' => ['bail', 'required', 'numeric', 'between:0,100', 'regex:/^(?:\d{1,3}|\d{0,3}\.\d{1,2})$/D'],
        ], [
            'monto.required' => 'Ingresa el monto de la cuenta.',
            'monto' => 'Usa un monto entre 0,01 y 999.999.999,99, con hasta dos decimales.',
            'porcentaje.required' => 'Ingresa el porcentaje de propina.',
            'porcentaje' => 'Usa un porcentaje entre 0 y 100, con hasta dos decimales.',
        ]);

        return view('propinas.index', [
            'datos' => $datos,
            'resultado' => new CalculoPropina($datos['monto'], $datos['porcentaje']),
        ]);
    }
}
