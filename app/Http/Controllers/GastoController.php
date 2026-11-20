<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GastoController extends Controller
{
    public function index(): View
    {
        $hoy = CarbonImmutable::today('America/Bogota');

        return view('gastos.index', [
            'hoy' => $hoy,
            'gastos' => Gasto::query()->orderByDesc('fecha')->orderByDesc('id')->paginate(15),
            'categorias' => Gasto::query()->select('categoria')->distinct()->orderBy('categoria')->pluck('categoria'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (is_string($request->input('monto'))) {
            $request->merge(['monto' => str_replace(',', '.', $request->input('monto'))]);
        }

        $datos = $request->validate([
            'concepto' => ['required', 'string', 'max:160'],
            'categoria' => ['required', 'string', 'max:80'],
            'monto' => ['bail', 'required', 'numeric', 'between:0.01,999999999.99', 'regex:/^(?:\d{1,9}|\d{0,9}\.\d{1,2})$/D'],
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:1000-01-01'],
        ], [
            'concepto.*' => 'Escribe un concepto de hasta 160 caracteres.',
            'categoria.*' => 'Escribe una categoría de hasta 80 caracteres.',
            'monto.*' => 'Ingresa un monto positivo con hasta nueve enteros y dos decimales.',
            'fecha.*' => 'Selecciona una fecha válida.',
        ]);

        Gasto::create($datos);

        return to_route('gastos.index')->with('estado', 'Gasto registrado.');
    }

    public function resumen(Request $request): View
    {
        $datos = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m', 'regex:/^[1-9]\d{3}-\d{2}$/D'],
        ], [
            'mes.*' => 'Selecciona un mes válido.',
        ]);

        $mes = isset($datos['mes'])
            ? CarbonImmutable::createFromFormat('!Y-m', $datos['mes'])
            : CarbonImmutable::today('America/Bogota')->startOfMonth();

        $categorias = Gasto::resumenMensual($mes);

        return view('gastos.resumen', [
            'mes' => $mes->format('Y-m'),
            'categorias' => $categorias,
            'total' => $categorias->first()->total ?? '0.00',
        ]);
    }
}
