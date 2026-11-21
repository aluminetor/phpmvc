@extends('layouts.app')

@section('title', 'Tablero de memoria')

@section('content')
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
        <h1 class="mb-0">Tablero de memoria</h1>
        <a href="{{ route('memoria.index', ['dificultad' => $partida->dificultad]) }}">Niveles y puntajes</a>
    </div>

    <div class="card card-body mb-3">
        <h2 id="estado-tablero" class="h5">{{ $nivel['nombre'] }}</h2>
        <p class="mb-2">Pares: {{ intdiv(count($partida->encontradas), 2) }} de {{ $nivel['pares'] }} · Intentos: {{ $partida->intentos }}</p>

        @if ($partida->terminada_en)
            <p class="mb-0 fw-semibold text-success" role="status">Partida completada en {{ $partida->segundosJugados() }} s.</p>
        @elseif (count($partida->seleccion) === 2)
            <p class="mb-0" role="status">Las cartas son distintas. Voltea otra para empezar el siguiente par.</p>
        @elseif (count($partida->seleccion) === 1)
            <p class="mb-0" role="status">Voltea una segunda carta.</p>
        @else
            <p class="mb-0" role="status">Voltea una carta para buscar un par.</p>
        @endif
    </div>

    <form method="POST" action="{{ route('memoria.voltear') }}" aria-labelledby="estado-tablero" class="mb-4">
        @csrf
        <input type="hidden" name="partida" value="{{ $partida->uuid }}">
        <input type="hidden" name="turno" value="{{ $partida->turno }}">
        <div class="row row-cols-4 g-2">
            @foreach ($cartas as $posicion => $carta)
                @php
                    $encontrada = in_array($posicion, $partida->encontradas, true);
                    $primera = count($partida->seleccion) === 1 && $partida->seleccion[0] === $posicion;
                    $etiqueta = 'Carta '.($posicion + 1).': '.($carta[1] ?? 'oculta').($encontrada ? ', par encontrado' : '');
                @endphp
                <div class="col">
                    <button type="submit" name="posicion" value="{{ $posicion }}" aria-label="{{ $etiqueta }}"
                            @class(['btn w-100 fs-3 py-2', 'btn-light border' => $carta && ! $encontrada, 'btn-outline-success' => $encontrada, 'btn-primary' => ! $carta])
                            @disabled($encontrada || $primera)>
                        <span aria-hidden="true">{{ $carta[0] ?? '?' }}</span>
                    </button>
                </div>
            @endforeach
        </div>
    </form>

    @if ($partida->terminada_en && ! $partida->puntaje_exists)
        <form method="POST" action="{{ route('memoria.puntajes.store') }}" class="card card-body">
            @csrf
            <input type="hidden" name="partida" value="{{ $partida->uuid }}">
            <label for="nombre-jugador" class="form-label fw-semibold">Nombre para la clasificación</label>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <input id="nombre-jugador" name="nombre" maxlength="80"
                       value="{{ is_scalar(old('nombre')) ? old('nombre') : '' }}"
                       class="form-control @error('nombre') is-invalid @enderror" required>
                <button type="submit" class="btn btn-primary text-nowrap">Guardar puntaje</button>
            </div>
        </form>
    @endif
@endsection
