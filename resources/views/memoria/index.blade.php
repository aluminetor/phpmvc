@extends('layouts.app')

@section('title', 'Juego de memoria')

@section('content')
    <h1 class="mb-4">Juego de memoria</h1>

    <form method="POST" action="{{ route('memoria.iniciar') }}" class="card card-body mb-4">
        @csrf
        <h2 class="h5 mb-3">Nueva partida</h2>
        <div class="d-flex flex-wrap gap-3 mb-3">
            @foreach ($niveles as $clave => $nivel)
                <div class="form-check">
                    <input id="nivel-{{ $clave }}" class="form-check-input" name="dificultad" type="radio"
                           value="{{ $clave }}" @checked(old('dificultad', $dificultad) === $clave) required>
                    <label for="nivel-{{ $clave }}" class="form-check-label">{{ $nivel['nombre'] }} · {{ $nivel['pares'] }} pares</label>
                </div>
            @endforeach
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Iniciar partida</button>
            @if ($tienePartida)
                <a href="{{ route('memoria.tablero') }}">Ver mi partida</a>
            @endif
        </div>
    </form>

    <section class="card card-body" aria-labelledby="mejores-puntajes">
        <h2 id="mejores-puntajes" class="h5 mb-3">Mejores puntajes</h2>
        <form method="GET" action="{{ route('memoria.index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-3">
            <div>
                <label for="nivel-puntajes" class="form-label">Dificultad</label>
                <select id="nivel-puntajes" name="dificultad" class="form-select">
                    @foreach ($niveles as $clave => $nivel)
                        <option value="{{ $clave }}" @selected($clave === $dificultad)>{{ $nivel['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-outline-success">Ver puntajes</button>
        </form>

        @if ($puntajes->isEmpty())
            <p class="mb-0 text-body-secondary">Aún no hay puntajes para este nivel.</p>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Jugador</th>
                            <th scope="col" class="text-end">Intentos</th>
                            <th scope="col" class="text-end">Tiempo (s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($puntajes as $puntaje)
                            <tr>
                                <th scope="row" class="text-break">{{ $puntaje->nombre }}</th>
                                <td class="text-end">{{ $puntaje->intentos }}</td>
                                <td class="text-end">{{ $puntaje->segundos }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
