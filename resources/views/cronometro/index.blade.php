@extends('layouts.app')

@section('title', 'Cronómetro online')

@section('content')
    <h1 class="mb-4">Cronómetro online</h1>

    <section class="card card-body mb-4" aria-labelledby="tiempo-transcurrido">
        <h2 id="tiempo-transcurrido" class="h5 mb-2">Tiempo transcurrido</h2>
        <output aria-labelledby="tiempo-transcurrido" aria-live="off" class="display-5 font-monospace fw-semibold mb-2"
                data-cronometro="{{ $transcurrido }}" data-en-marcha="{{ $cronometro->enMarcha() ? '1' : '0' }}">{{ \App\Models\Cronometro::formatear($transcurrido) }}</output>
        <p class="text-body-secondary" role="status">{{ $cronometro->enMarcha() ? 'En marcha' : 'Detenido' }}</p>

        <div class="d-flex flex-wrap gap-2">
            @if ($cronometro->enMarcha())
                <form method="POST" action="{{ route('cronometro.pausar') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="version" value="{{ $cronometro->version }}">
                    <button type="submit" class="btn btn-primary">Pausar</button>
                </form>
                <form method="POST" action="{{ route('cronometro.vueltas.store') }}">
                    @csrf
                    <input type="hidden" name="version" value="{{ $cronometro->version }}">
                    <button type="submit" class="btn btn-outline-success">Registrar vuelta</button>
                </form>
            @else
                <form method="POST" action="{{ route('cronometro.iniciar') }}">
                    @csrf
                    <input type="hidden" name="version" value="{{ $cronometro->version }}">
                    <button type="submit" class="btn btn-primary">{{ $transcurrido > 0 ? 'Reanudar' : 'Iniciar' }}</button>
                </form>
            @endif

            <form method="POST" action="{{ route('cronometro.reiniciar') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="version" value="{{ $cronometro->version }}">
                <button type="submit" class="btn btn-outline-danger" @if($vueltas->total() > 0) aria-describedby="efecto-reinicio" @endif>Reiniciar</button>
            </form>
        </div>

        @if ($vueltas->total() > 0)
            <p id="efecto-reinicio" class="form-text mb-0 mt-2">Reiniciar también borra las vueltas registradas.</p>
        @endif
    </section>

    <h2 class="h5 mb-3">Vueltas registradas</h2>

    @if ($vueltas->isEmpty())
        <p class="text-body-secondary">Aún no hay vueltas registradas.</p>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Vuelta</th>
                        <th scope="col" class="text-end">Tiempo de vuelta</th>
                        <th scope="col" class="text-end">Tiempo acumulado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vueltas as $vuelta)
                        <tr>
                            <th scope="row">{{ $vuelta->numero }}</th>
                            <td class="text-end font-monospace">{{ \App\Models\Cronometro::formatear($vuelta->parcial_ms) }}</td>
                            <td class="text-end font-monospace">{{ \App\Models\Cronometro::formatear($vuelta->total_ms) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{ $vueltas->links('pagination::bootstrap-5') }}
@endsection

@push('scripts')
    <script src="{{ asset('js/cronometro.js') }}" defer></script>
@endpush
