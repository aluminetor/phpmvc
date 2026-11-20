<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Servicio;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservaController extends Controller
{
    public function index(): View
    {
        $ahora = CarbonImmutable::now(Servicio::ZONA_HORARIA);

        return $this->pagina($ahora, $ahora->startOfDay());
    }

    public function disponibilidad(Request $request): View
    {
        $ahora = CarbonImmutable::now(Servicio::ZONA_HORARIA);
        $datos = $request->validate([
            'servicio_id' => ['required', 'integer', 'exists:servicios,id'],
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$ahora->toDateString()],
        ], [
            'servicio_id.*' => 'Selecciona un servicio del catálogo.',
            'fecha.*' => 'Selecciona una fecha válida a partir de hoy.',
        ]);

        $fecha = CarbonImmutable::createFromFormat('!Y-m-d', $datos['fecha'], Servicio::ZONA_HORARIA);

        return $this->pagina($ahora, $fecha, (int) $datos['servicio_id']);
    }

    public function store(Request $request): RedirectResponse
    {
        $ahora = CarbonImmutable::now(Servicio::ZONA_HORARIA);
        $datos = $request->validate([
            'servicio_id' => ['required', 'integer', 'exists:servicios,id'],
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$ahora->toDateString()],
            'hora' => ['required', 'date_format:H:i'],
            'nombre' => ['required', 'string', 'max:120'],
        ], [
            'servicio_id.*' => 'Selecciona un servicio del catálogo.',
            'fecha.*' => 'Selecciona una fecha válida a partir de hoy.',
            'hora.*' => 'Selecciona un horario de la lista.',
            'nombre.*' => 'Escribe un nombre de hasta 120 caracteres.',
        ]);

        $servicio = Servicio::findOrFail($datos['servicio_id']);
        $fecha = CarbonImmutable::createFromFormat('!Y-m-d', $datos['fecha'], Servicio::ZONA_HORARIA);
        $destino = ['servicio_id' => $servicio->getKey(), 'fecha' => $datos['fecha']];

        if (! in_array($datos['hora'], $servicio->horariosAgendables($fecha, $ahora), true)) {
            return to_route('reservas.disponibilidad', $destino)
                ->withErrors(['hora' => 'El horario elegido ya pasó o está fuera del horario de atención.'])
                ->withInput();
        }

        try {
            $servicio->reservas()->create([
                'nombre' => $datos['nombre'],
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'].':00',
            ]);
        } catch (UniqueConstraintViolationException) {
            return to_route('reservas.disponibilidad', $destino)
                ->withErrors(['hora' => 'El horario ya está reservado. Elige otro.'])
                ->withInput();
        }

        return to_route('reservas.disponibilidad', $destino)
            ->with('estado', 'Reserva agendada. Puedes confirmarla en la lista.');
    }

    public function confirmar(Reserva $reserva): RedirectResponse
    {
        $ahora = CarbonImmutable::now(Servicio::ZONA_HORARIA);

        if ($reserva->confirmada_en === null && $reserva->inicio()->lessThanOrEqualTo($ahora)) {
            return to_route('reservas.index')->withErrors(['reserva' => 'La cita ya pasó.']);
        }

        Reserva::query()->whereKey($reserva->getKey())->whereNull('confirmada_en')
            ->update(['confirmada_en' => $ahora->utc()]);

        return to_route('reservas.index')->with('estado', 'Reserva confirmada.');
    }

    private function pagina(CarbonImmutable $ahora, CarbonImmutable $fecha, ?int $servicioId = null): View
    {
        $servicios = Servicio::query()->orderBy('nombre')->get();
        $servicio = $servicioId === null ? $servicios->first() : $servicios->find($servicioId);
        $reservas = Reserva::query()->with('servicio')
            ->orderByDesc('fecha')->orderByDesc('hora')->orderByDesc('id')
            ->paginate(15);

        if ($servicioId !== null) {
            $reservas->appends(['servicio_id' => $servicioId, 'fecha' => $fecha->toDateString()]);
        }

        return view('reservas.index', [
            'ahora' => $ahora,
            'fecha' => $fecha,
            'servicios' => $servicios,
            'servicio' => $servicio,
            'horarios' => $servicio?->horariosDisponibles($fecha, $ahora) ?? [],
            'reservas' => $reservas,
        ]);
    }
}
