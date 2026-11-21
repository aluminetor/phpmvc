<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecetaController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'tipo' => ['nullable', 'string', 'max:80'],
            'ingrediente' => ['nullable', 'string', 'max:160'],
            'guardadas' => ['nullable', 'boolean'],
        ], [
            'tipo.*' => 'Selecciona un tipo de comida de hasta 80 caracteres.',
            'ingrediente.*' => 'Escribe un ingrediente de hasta 160 caracteres.',
            'guardadas.*' => 'El filtro de recetas guardadas no es válido.',
        ]);

        $tipo = $filtros['tipo'] ?? null;
        $ingrediente = $filtros['ingrediente'] ?? null;
        $guardadas = (bool) ($filtros['guardadas'] ?? false);

        $recetas = Receta::query()
            ->when($tipo !== null, fn (Builder $query) => $query->where('tipo', $tipo))
            ->when($ingrediente !== null, fn (Builder $query) => $query->conIngrediente($ingrediente))
            ->when($guardadas, fn (Builder $query) => $query->has('guardado'))
            ->withExists('guardado')
            ->orderByDesc('id')->paginate(12)->appends($filtros);

        return view('recetas.index', [
            'recetas' => $recetas,
            'tipos' => Receta::query()->select('tipo')->distinct()->orderBy('tipo')->pluck('tipo'),
            'tipo' => $tipo,
            'ingrediente' => $ingrediente,
            'guardadas' => $guardadas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'tipo' => ['required', 'string', 'max:80'],
            'ingredientes' => ['required', 'string', 'max:5000'],
            'preparacion' => ['required', 'string', 'max:10000'],
        ], [
            'titulo.*' => 'Escribe un título de hasta 160 caracteres.',
            'tipo.*' => 'Escribe un tipo de comida de hasta 80 caracteres.',
            'ingredientes.*' => 'Escribe los ingredientes en hasta 5.000 caracteres.',
            'preparacion.*' => 'Escribe la preparación en hasta 10.000 caracteres.',
        ]);

        $receta = Receta::create($datos);

        return to_route('recetas.show', $receta)->with('estado', 'Receta publicada.');
    }

    public function show(Receta $receta): View
    {
        $receta->loadExists('guardado');

        return view('recetas.show', [
            'receta' => $receta,
            'ingredientes' => array_filter(array_map('trim', preg_split('/\R/u', $receta->ingredientes)), fn (string $linea): bool => $linea !== ''),
        ]);
    }

    public function guardar(Receta $receta): RedirectResponse
    {
        $receta->guardado()->firstOrCreate();

        return to_route('recetas.show', $receta)->with('estado', 'Receta guardada.');
    }

    public function quitarGuardado(Receta $receta): RedirectResponse
    {
        $receta->guardado()->delete();

        return to_route('recetas.show', $receta)->with('estado', 'Receta retirada de las guardadas.');
    }
}
