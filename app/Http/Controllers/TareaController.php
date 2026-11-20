<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TareaController extends Controller
{
    public function index(): View
    {
        return view('tareas.index', [
            'tareas' => Tarea::query()->orderByDesc('id')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
        ], [
            'titulo.required' => 'Escribe el título de la tarea.',
            'titulo.string' => 'El título debe ser texto.',
            'titulo.max' => 'El título admite hasta 160 caracteres.',
        ]);

        Tarea::create($datos);

        return to_route('tareas.index')->with('estado', 'Tarea agregada.');
    }

    public function update(Request $request, Tarea $tarea): RedirectResponse
    {
        $datos = $request->validate([
            'completada' => ['required', 'boolean'],
        ], [
            'completada.required' => 'Indica el estado de la tarea.',
            'completada.boolean' => 'El estado indicado no es válido.',
        ]);

        $tarea->update($datos);

        return to_route('tareas.index')->with('estado', $tarea->completada
            ? 'Tarea completada.'
            : 'Tarea marcada como pendiente.');
    }

    public function destroy(Tarea $tarea): RedirectResponse
    {
        $tarea->delete();

        return to_route('tareas.index')->with('estado', 'Tarea eliminada.');
    }
}
