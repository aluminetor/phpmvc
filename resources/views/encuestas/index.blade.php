@extends('layouts.app')

@section('title', 'Plataforma de encuestas')

@section('content')
    <h1 class="mb-4">Plataforma de encuestas</h1>

    <form method="POST" action="{{ route('encuestas.store') }}" class="card card-body mb-4">
        @csrf
        <h2 class="h5 mb-3">Crear encuesta</h2>
        <div class="mb-3">
            <label for="pregunta-encuesta" class="form-label">Pregunta</label>
            <input id="pregunta-encuesta" name="pregunta" maxlength="200" required
                   class="form-control @error('pregunta') is-invalid @enderror"
                   value="{{ is_scalar(old('pregunta')) ? old('pregunta') : '' }}">
        </div>
        <fieldset>
            <legend class="h6">Opciones de respuesta</legend>
            <div class="row g-3">
                @for ($i = 0; $i < 8; $i++)
                    @php($opcion = old('opciones.' . $i))
                    <div class="col-sm-6">
                        <label for="opcion-{{ $i }}" class="form-label">Opción {{ $i + 1 }}{{ $i >= 2 ? ' (opcional)' : '' }}</label>
                        <input id="opcion-{{ $i }}" name="opciones[]" maxlength="120"
                               class="form-control @error('opciones.' . $i) is-invalid @enderror"
                               value="{{ is_scalar($opcion) ? $opcion : '' }}" @required($i < 2)>
                    </div>
                @endfor
            </div>
        </fieldset>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Publicar encuesta</button>
        </div>
    </form>

    <h2 class="h5 mb-3">Encuestas publicadas</h2>
    @if ($encuestas->isEmpty())
        <p class="text-body-secondary">Aún no hay encuestas publicadas.</p>
    @else
        <ul class="list-group mb-3">
            @foreach ($encuestas as $encuesta)
                <li class="list-group-item d-flex flex-wrap align-items-center gap-2 justify-content-between">
                    <a href="{{ route('encuestas.show', $encuesta) }}" class="fw-semibold text-break">{{ $encuesta->pregunta }}</a>
                    <a href="{{ route('encuestas.resultados', $encuesta) }}">Resultados ({{ $encuesta->votos_count }})</a>
                </li>
            @endforeach
        </ul>
    @endif

    {{ $encuestas->links('pagination::bootstrap-5') }}
@endsection
