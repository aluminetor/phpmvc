@extends('layouts.app')

@section('title', 'Lista de tareas')

@section('content')
    <h1 class="mb-4">Lista de tareas</h1>

    <form method="POST" action="{{ route('tareas.store') }}" class="card card-body mb-4">
        @csrf
        <label for="titulo" class="form-label fw-semibold">Nueva tarea</label>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <input id="titulo" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                   type="text" value="{{ is_scalar(old('titulo')) ? old('titulo') : '' }}" maxlength="160" required>
            <button class="btn btn-primary" type="submit">Agregar</button>
        </div>
    </form>

    @if ($tareas->isEmpty())
        <p class="text-body-secondary">Aún no hay tareas en esta página.</p>
    @else
        <ul class="list-group mb-3">
            @foreach ($tareas as $tarea)
                <li class="list-group-item p-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div class="text-break">
                        <span @class(['text-decoration-line-through' => $tarea->completada])>{{ $tarea->titulo }}</span>
                        <span @class(['badge ms-0 ms-sm-2', 'text-bg-success' => $tarea->completada, 'text-bg-light' => ! $tarea->completada])>
                            {{ $tarea->completada ? 'Completada' : 'Pendiente' }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                        <form method="POST" action="{{ route('tareas.update', $tarea) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="completada" value="{{ $tarea->completada ? '0' : '1' }}">
                            <button class="btn btn-sm btn-outline-success" type="submit"
                                    aria-label="{{ $tarea->completada ? 'Reabrir' : 'Completar' }}: {{ $tarea->titulo }}">
                                {{ $tarea->completada ? 'Reabrir' : 'Completar' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('tareas.destroy', $tarea) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit"
                                    aria-label="Eliminar: {{ $tarea->titulo }}">Eliminar</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    {{ $tareas->links('pagination::bootstrap-5') }}
@endsection
