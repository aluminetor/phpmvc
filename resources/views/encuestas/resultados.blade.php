@extends('layouts.app')

@section('title', 'Resultados de la encuesta')

@section('content')
    <a href="{{ route('encuestas.show', $encuesta) }}" class="d-inline-block mb-3">Volver a la encuesta</a>
    <section class="card card-body" aria-labelledby="resultados-encuesta">
        <h1 id="resultados-encuesta" class="h3 text-break">{{ $encuesta->pregunta }}</h1>
        <p class="text-body-secondary mb-4">Total de votos: {{ $total }}</p>

        @foreach ($opciones as $opcion)
            @php($porcentaje = $total > 0 ? round(100 * $opcion->votos_count / $total, 1) : 0)
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2 justify-content-between mb-1">
                    <span id="resultado-{{ $opcion->id }}" class="fw-semibold text-break">{{ $opcion->texto }}</span>
                    <span class="text-nowrap">{{ $opcion->votos_count }} {{ $opcion->votos_count === 1 ? 'voto' : 'votos' }} · {{ number_format($porcentaje, 1, ',', '.') }} %</span>
                </div>
                <div class="progress" role="progressbar" aria-labelledby="resultado-{{ $opcion->id }}"
                     aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                </div>
            </div>
        @endforeach
    </section>
@endsection
