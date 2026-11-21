@extends('layouts.app')

@section('title', 'Calendario de eventos')

@section('content')
    <h1 class="mb-4">Calendario de eventos</h1>

    @if ($recordatorios->isNotEmpty())
        <section class="alert alert-warning" aria-labelledby="recordatorios-activos">
            <h2 id="recordatorios-activos" class="h5">Recordatorios activos</h2>
            <ul class="mb-0">
                @foreach ($recordatorios as $evento)
                    <li>
                        <a href="{{ route('eventos.edit', $evento) }}" class="alert-link">{{ $evento->titulo }}</a>
                        <time datetime="{{ $evento->inicioLocal()->toIso8601String() }}">{{ $evento->inicioLocal()->format('d/m/Y H:i') }}</time>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="mb-4" aria-labelledby="mes-eventos">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
            <h2 id="mes-eventos" class="h4 text-capitalize mb-0">{{ $mes->locale('es')->translatedFormat('F \d\e Y') }}</h2>
            <nav aria-label="Mes del calendario" class="d-flex flex-wrap gap-2">
                @if ($mes->format('Y-m') !== '1000-01')
                    <a class="btn btn-sm btn-outline-success" href="{{ route('eventos.index', ['mes' => $mes->subMonth()->format('Y-m')]) }}">Anterior</a>
                @endif
                <a class="btn btn-sm btn-outline-success" href="{{ route('eventos.index') }}">Mes actual</a>
                @if ($mes->format('Y-m') !== '9999-12')
                    <a class="btn btn-sm btn-outline-success" href="{{ route('eventos.index', ['mes' => $mes->addMonth()->format('Y-m')]) }}">Siguiente</a>
                @endif
            </nav>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0" aria-labelledby="mes-eventos" style="min-width: 42rem; table-layout: fixed;">
                <thead>
                    <tr>
                        @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $nombreDia)
                            <th scope="col">{{ $nombreDia }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($semanas as $semana)
                        <tr>
                            @foreach ($semana as $dia)
                                <td class="align-top">
                                    @if ($dia !== null)
                                        @php($fecha = $mes->setDay($dia))
                                        <time datetime="{{ $fecha->toDateString() }}" @class(['fw-bold', 'text-success' => $fecha->isSameDay($ahora)]) @if($fecha->isSameDay($ahora)) aria-current="date" @endif>{{ $dia }}</time>
                                        <ul class="list-unstyled small mb-0" style="min-height: 2.25rem;">
                                            @foreach ($porDia->get($dia, []) as $evento)
                                                <li class="mt-1">
                                                    <time datetime="{{ $evento->inicioLocal()->toIso8601String() }}">{{ $evento->inicioLocal()->format('H:i') }}</time>
                                                    <a href="{{ route('eventos.edit', $evento) }}" class="text-break">{{ $evento->titulo }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <form method="POST" action="{{ route('eventos.store') }}" class="card card-body">
        @csrf
        <h2 class="h5 mb-3">Agregar evento</h2>
        @include('eventos.formulario', ['evento' => null])
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Agregar evento</button>
        </div>
    </form>
@endsection
