<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventoController extends Controller
{
    public function index(Request $request): View
    {
        $datos = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m', 'regex:/^[1-9]\d{3}-\d{2}$/D'],
        ], ['mes.*' => 'Selecciona un mes válido.']);

        $ahora = CarbonImmutable::now(Evento::ZONA_HORARIA);
        $mes = isset($datos['mes'])
            ? CarbonImmutable::createFromFormat('!Y-m', $datos['mes'], Evento::ZONA_HORARIA)
            : $ahora->startOfMonth();

        $dias = array_merge(
            array_fill(0, $mes->dayOfWeekIso - 1, null),
            range(1, $mes->daysInMonth),
        );
        $dias = array_pad($dias, (int) ceil(count($dias) / 7) * 7, null);

        return view('eventos.index', [
            'mes' => $mes,
            'ahora' => $ahora,
            'semanas' => array_chunk($dias, 7),
            'porDia' => Evento::delMes($mes)->groupBy(fn (Evento $evento): int => $evento->inicioLocal()->day),
            'recordatorios' => Evento::conRecordatorioActivo($ahora),
            'opcionesRecordatorio' => Evento::RECORDATORIOS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $evento = Evento::create($this->validar($request));

        return to_route('eventos.index', ['mes' => $evento->inicioLocal()->format('Y-m')])
            ->with('estado', 'Evento agregado.');
    }

    public function edit(Evento $evento): View
    {
        return view('eventos.edit', [
            'evento' => $evento,
            'opcionesRecordatorio' => Evento::RECORDATORIOS,
        ]);
    }

    public function update(Request $request, Evento $evento): RedirectResponse
    {
        $evento->update($this->validar($request));

        return to_route('eventos.index', ['mes' => $evento->inicioLocal()->format('Y-m')])
            ->with('estado', 'Evento actualizado.');
    }

    public function destroy(Evento $evento): RedirectResponse
    {
        $mes = $evento->inicioLocal()->format('Y-m');
        $evento->delete();

        return to_route('eventos.index', ['mes' => $mes])->with('estado', 'Evento eliminado.');
    }

    private function validar(Request $request): array
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'inicio_en' => ['required', 'date_format:Y-m-d\TH:i', 'regex:/^[1-9]\d{3}-/D'],
            'recordatorio_minutos' => ['nullable', 'integer', Rule::in(array_keys(Evento::RECORDATORIOS))],
        ], [
            'titulo.*' => 'Escribe un título de hasta 160 caracteres.',
            'descripcion.*' => 'La descripción admite hasta 3.000 caracteres.',
            'inicio_en.*' => 'Selecciona una fecha y una hora válidas.',
            'recordatorio_minutos.*' => 'Selecciona un recordatorio de la lista.',
        ]);

        $inicio = CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $datos['inicio_en'], Evento::ZONA_HORARIA)->utc();

        if ($inicio->year > 9999) {
            throw ValidationException::withMessages(['inicio_en' => 'Selecciona una fecha dentro del calendario.']);
        }

        return [
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'] ?? null,
            'inicio_en' => $inicio,
            'recordatorio_minutos' => $datos['recordatorio_minutos'] ?? null,
        ];
    }
}
