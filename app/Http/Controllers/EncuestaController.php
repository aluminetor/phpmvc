<?php

namespace App\Http\Controllers;

use App\Http\Middleware\IdentificarNavegador;
use App\Models\Encuesta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EncuestaController extends Controller
{
    public function index(): View
    {
        return view('encuestas.index', [
            'encuestas' => Encuesta::query()->withCount('votos')->latest('id')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'pregunta' => ['required', 'string', 'max:200'],
            'opciones' => ['required', 'array', 'list', 'min:2', 'max:8'],
            'opciones.0' => ['required'],
            'opciones.1' => ['required'],
            'opciones.*' => ['nullable', 'string', 'max:120', 'distinct:ignore_case'],
        ], [
            'pregunta.*' => 'Escribe una pregunta de hasta 200 caracteres.',
            'opciones' => 'Incluye entre dos y ocho opciones.',
            'opciones.*.required' => 'Completa las dos primeras opciones.',
            'opciones.*.distinct' => 'Las opciones de la encuesta deben ser diferentes.',
            'opciones.*.max' => 'Cada opción admite hasta 120 caracteres.',
            'opciones.*.string' => 'Escribe la opción como texto.',
        ]);

        $encuesta = DB::transaction(function () use ($datos): Encuesta {
            $encuesta = Encuesta::create(['pregunta' => $datos['pregunta']]);

            $opciones = array_filter($datos['opciones'], fn (?string $texto): bool => $texto !== null);
            $encuesta->opciones()->createMany(array_map(fn (string $texto): array => ['texto' => $texto], $opciones));

            return $encuesta;
        });

        return to_route('encuestas.show', $encuesta)->with('estado', 'Encuesta publicada.');
    }

    public function show(Request $request, Encuesta $encuesta): View
    {
        return view('encuestas.show', [
            'encuesta' => $encuesta,
            'opciones' => $encuesta->opciones()->orderBy('id')->get(),
            'voto' => $encuesta->votos()->where('navegador_id', IdentificarNavegador::id($request))->first(),
        ]);
    }

    public function votar(Request $request, Encuesta $encuesta): RedirectResponse
    {
        $datos = $request->validate([
            'opcion_encuesta_id' => [
                'bail', 'required', 'integer',
                Rule::exists('opciones_encuesta', 'id')->where('encuesta_id', $encuesta->id),
            ],
        ], ['opcion_encuesta_id.*' => 'Selecciona una opción de esta encuesta.']);

        $voto = $encuesta->votos()->firstOrCreate(
            ['navegador_id' => IdentificarNavegador::id($request)],
            ['opcion_encuesta_id' => $datos['opcion_encuesta_id']],
        );

        return to_route('encuestas.resultados', $encuesta)->with('estado', $voto->wasRecentlyCreated
            ? 'Voto registrado.'
            : 'Este navegador ya registró un voto en esta encuesta.');
    }

    public function resultados(Encuesta $encuesta): View
    {
        $opciones = $encuesta->opciones()->withCount('votos')->orderBy('id')->get();

        return view('encuestas.resultados', [
            'encuesta' => $encuesta,
            'opciones' => $opciones,
            'total' => $opciones->sum('votos_count'),
        ]);
    }
}
