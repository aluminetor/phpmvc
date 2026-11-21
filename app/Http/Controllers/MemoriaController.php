<?php

namespace App\Http\Controllers;

use App\Models\PartidaMemoria;
use App\Models\PuntajeMemoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemoriaController extends Controller
{
    public function index(Request $request): View
    {
        $datos = $request->validate([
            'dificultad' => ['nullable', 'string', Rule::in(array_keys(PartidaMemoria::NIVELES))],
        ], ['dificultad.*' => 'Selecciona un nivel de dificultad de la lista.']);

        $dificultad = $datos['dificultad'] ?? 'facil';

        return view('memoria.index', [
            'niveles' => PartidaMemoria::NIVELES,
            'dificultad' => $dificultad,
            'tienePartida' => $request->session()->has('memoria.partida'),
            'puntajes' => PuntajeMemoria::query()->where('dificultad', $dificultad)
                ->orderBy('intentos')->orderBy('segundos')->orderBy('id')->limit(10)->get(),
        ]);
    }

    public function iniciar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'dificultad' => ['required', 'string', Rule::in(array_keys(PartidaMemoria::NIVELES))],
        ], ['dificultad.*' => 'Selecciona un nivel de dificultad de la lista.']);

        $partida = PartidaMemoria::nueva($datos['dificultad']);
        $request->session()->put('memoria.partida', $partida->uuid);

        return to_route('memoria.tablero');
    }

    public function tablero(Request $request): View|RedirectResponse
    {
        $partida = PartidaMemoria::query()
            ->where('uuid', $request->session()->get('memoria.partida'))
            ->withExists('puntaje')->first();

        if ($partida === null) {
            $request->session()->forget('memoria.partida');

            return to_route('memoria.index');
        }

        return view('memoria.tablero', [
            'partida' => $partida,
            'nivel' => PartidaMemoria::NIVELES[$partida->dificultad],
            'cartas' => $partida->cartasVisibles(),
        ]);
    }

    public function voltear(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'partida' => ['required', 'uuid'],
            'posicion' => ['required', 'integer', 'min:0'],
            'turno' => ['required', 'integer', 'min:0'],
        ], [
            'partida.*' => 'La partida no es válida.',
            'posicion.*' => 'Selecciona una carta del tablero.',
            'turno.*' => 'El turno no es válido.',
        ]);

        if ($request->session()->get('memoria.partida') !== $datos['partida']) {
            return to_route('memoria.tablero')->withErrors(['partida' => 'La partida cambió. Usa el tablero actual.']);
        }

        $volteada = DB::transaction(function () use ($datos): bool {
            $partida = PartidaMemoria::query()->where('uuid', $datos['partida'])->lockForUpdate()->first();

            return $partida?->voltear((int) $datos['posicion'], (int) $datos['turno']) ?? false;
        });

        if (! $volteada) {
            return to_route('memoria.tablero')->withErrors(['posicion' => 'Esa jugada ya pasó o la carta no está disponible.']);
        }

        return to_route('memoria.tablero');
    }

    public function guardarPuntaje(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'partida' => ['required', 'uuid'],
            'nombre' => ['required', 'string', 'max:80'],
        ], [
            'partida.*' => 'La partida no es válida.',
            'nombre.*' => 'Escribe un nombre de hasta 80 caracteres.',
        ]);

        if ($request->session()->get('memoria.partida') !== $datos['partida']) {
            return to_route('memoria.tablero')->withErrors(['partida' => 'La partida cambió. Usa el tablero actual.']);
        }

        $partida = PartidaMemoria::query()->where('uuid', $datos['partida'])->first();

        if ($partida?->terminada_en === null) {
            return to_route('memoria.tablero')->withErrors(['partida' => 'Completa la partida para guardar el puntaje.']);
        }

        $partida->puntaje()->firstOrCreate([], [
            'nombre' => $datos['nombre'],
            'dificultad' => $partida->dificultad,
            'intentos' => $partida->intentos,
            'segundos' => $partida->segundosJugados(),
        ]);

        return to_route('memoria.index', ['dificultad' => $partida->dificultad])
            ->with('estado', 'Puntaje guardado.');
    }
}
