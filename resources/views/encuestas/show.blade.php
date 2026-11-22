@extends('layouts.app')

@section('title', $encuesta->pregunta)

@section('content')
    <a href="{{ route('encuestas.index') }}" class="d-inline-block mb-3">Volver a encuestas</a>
    <div class="card card-body">
        <h1 class="h3 text-break mb-3">{{ $encuesta->pregunta }}</h1>

        @if ($voto)
            <p class="mb-3">Tu respuesta: <strong>{{ $opciones->find($voto->opcion_encuesta_id)?->texto }}</strong></p>
            <a href="{{ route('encuestas.resultados', $encuesta) }}" class="align-self-start">Ver resultados</a>
        @else
            <form method="POST" action="{{ route('encuestas.votar', $encuesta) }}">
                @csrf
                <fieldset>
                    <legend class="h6">Elige una opción</legend>
                    @foreach ($opciones as $opcion)
                        <div class="form-check mb-2">
                            <input id="opcion-{{ $opcion->id }}" name="opcion_encuesta_id" value="{{ $opcion->id }}"
                                   type="radio" class="form-check-input" required>
                            <label for="opcion-{{ $opcion->id }}" class="form-check-label text-break">{{ $opcion->texto }}</label>
                        </div>
                    @endforeach
                </fieldset>
                <div class="d-flex flex-wrap gap-3 align-items-center mt-3">
                    <button type="submit" class="btn btn-primary">Votar</button>
                    <a href="{{ route('encuestas.resultados', $encuesta) }}">Ver resultados</a>
                </div>
            </form>
        @endif
    </div>
@endsection
