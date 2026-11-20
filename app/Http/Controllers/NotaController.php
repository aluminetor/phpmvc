<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotaController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:160'],
            'categoria' => ['nullable', 'string', 'max:80'],
        ], [
            'q.*' => 'Escribe una búsqueda de hasta 160 caracteres.',
            'categoria.*' => 'Filtra por una categoría de hasta 80 caracteres.',
        ]);

        $palabra = $filtros['q'] ?? null;
        $categoria = $filtros['categoria'] ?? null;

        return view('notas.index', [
            'palabra' => $palabra,
            'categoria' => $categoria,
            'notas' => Nota::query()
                ->when($palabra !== null, fn (Builder $query) => $query->buscar($palabra))
                ->when($categoria !== null, fn (Builder $query) => $query->where('categoria', $categoria))
                ->orderByDesc('id')->paginate(10)->appends($filtros),
            'categorias' => Nota::query()->select('categoria')->distinct()->orderBy('categoria')->pluck('categoria'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'contenido' => ['required', 'string', 'max:10000'],
            'categoria' => ['required', 'string', 'max:80'],
        ], [
            'titulo.*' => 'Escribe un título de hasta 160 caracteres.',
            'contenido.*' => 'Escribe una nota de hasta 10.000 caracteres.',
            'categoria.*' => 'Escribe una categoría de hasta 80 caracteres.',
        ]);

        Nota::create($datos);

        return to_route('notas.index')->with('estado', 'Nota guardada.');
    }
}
