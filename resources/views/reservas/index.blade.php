@extends('layouts.app')

@section('title', 'Sistema de reservas')

@section('content')
    <h1 class="mb-4">Sistema de reservas</h1>

    <section class="card card-body mb-4" aria-labelledby="disponibilidad">
        <h2 id="disponibilidad" class="h5 mb-2">Consultar disponibilidad</h2>
        <p class="text-body-secondary">Lunes a viernes, de 09:00 a 17:00 (hora de Bogotá). Cada cita dura 30 minutos.</p>

        @if ($servicios->isEmpty())
            <p class="mb-0">Aún no hay servicios disponibles.</p>
        @else
            <form method="GET" action="{{ route('reservas.disponibilidad') }}" class="row g-3 align-items-end">
                <div class="col-sm">
                    <label for="servicio-id" class="form-label">Servicio</label>
                    <select id="servicio-id" name="servicio_id" class="form-select @error('servicio_id') is-invalid @enderror" required>
                        @foreach ($servicios as $opcion)
                            <option value="{{ $opcion->id }}" @selected($opcion->id === $servicio?->id)>{{ $opcion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm">
                    <label for="fecha-reserva" class="form-label">Fecha</label>
                    <input id="fecha-reserva" name="fecha" type="date" min="{{ $ahora->toDateString() }}"
                           max="9999-12-31" value="{{ $fecha->toDateString() }}"
                           class="form-control @error('fecha') is-invalid @enderror" required>
                </div>
                <div class="col-sm-auto">
                    <button class="btn btn-outline-success" type="submit">Consultar</button>
                </div>
            </form>
        @endif
    </section>

    @if ($servicio)
        <section class="card card-body mb-4" aria-labelledby="agendar">
            <h2 id="agendar" class="h5 mb-2">Agendar cita</h2>
            <p class="text-body-secondary">{{ $servicio->nombre }} · <time datetime="{{ $fecha->toDateString() }}">{{ $fecha->format('d/m/Y') }}</time></p>

            @if ($horarios === [])
                <p class="mb-0">No hay horarios disponibles para ese servicio y fecha.</p>
            @else
                <form method="POST" action="{{ route('reservas.store') }}" class="row g-3 align-items-end">
                    @csrf
                    <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
                    <input type="hidden" name="fecha" value="{{ $fecha->toDateString() }}">
                    <div class="col-sm">
                        <label for="nombre-reserva" class="form-label">Nombre</label>
                        <input id="nombre-reserva" name="nombre" maxlength="120" autocomplete="name"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ is_scalar(old('nombre')) ? old('nombre') : '' }}" required>
                    </div>
                    <div class="col-sm">
                        <label for="hora-reserva" class="form-label">Horario</label>
                        <select id="hora-reserva" name="hora" class="form-select @error('hora') is-invalid @enderror" required>
                            <option value="">Selecciona un horario</option>
                            @foreach ($horarios as $hora)
                                <option value="{{ $hora }}" @selected(old('hora') === $hora)>{{ $hora }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-auto">
                        <button class="btn btn-primary" type="submit">Agendar</button>
                    </div>
                </form>
            @endif
        </section>
    @endif

    <h2 class="h5 mb-3">Reservas registradas</h2>
    @if ($reservas->isEmpty())
        <p class="text-body-secondary">Aún no hay reservas registradas.</p>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col">Cita</th>
                        <th scope="col">Servicio</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservas as $reserva)
                        @php($inicio = $reserva->inicio())
                        <tr>
                            <td class="text-nowrap"><time datetime="{{ $inicio->toIso8601String() }}">{{ $inicio->format('d/m/Y H:i') }}</time></td>
                            <td>{{ $reserva->servicio->nombre }}</td>
                            <td class="text-break">{{ $reserva->nombre }}</td>
                            @if ($reserva->confirmada_en)
                                <td><span class="badge text-bg-success">Confirmada</span></td>
                                <td></td>
                            @elseif ($inicio->lessThanOrEqualTo($ahora))
                                <td><span class="badge text-bg-light">Vencida</span></td>
                                <td></td>
                            @else
                                <td><span class="badge text-bg-warning">Pendiente</span></td>
                                <td>
                                    <form method="POST" action="{{ route('reservas.confirmar', $reserva) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success" type="submit"
                                                aria-label="Confirmar cita de {{ $reserva->nombre }} el {{ $inicio->format('d/m/Y H:i') }}">Confirmar</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{ $reservas->links('pagination::bootstrap-5') }}
@endsection
