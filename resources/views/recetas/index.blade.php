@extends('layouts.app')

@section('title', 'Plataforma de recetas')

@section('content')
    <h1 class="mb-4">Plataforma de recetas</h1>

    <form method="GET" action="{{ route('recetas.index') }}" class="card card-body mb-4">
        <h2 class="h5 mb-3">Buscar recetas</h2>
        <div class="row g-3 align-items-end">
            <div class="col-sm">
                <label for="filtrar-tipo" class="form-label">Tipo de comida</label>
                <input id="filtrar-tipo" name="tipo" list="tipos-receta" class="form-control" value="{{ $tipo }}" maxlength="80">
            </div>
            <div class="col-sm">
                <label for="filtrar-ingrediente" class="form-label">Ingrediente</label>
                <input id="filtrar-ingrediente" name="ingrediente" type="search" class="form-control" value="{{ $ingrediente }}" maxlength="160">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input id="solo-guardadas" name="guardadas" value="1" type="checkbox" class="form-check-input" @checked($guardadas)>
                    <label for="solo-guardadas" class="form-check-label">Solo recetas guardadas</label>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
            <button type="submit" class="btn btn-outline-success">Filtrar recetas</button>
            @if ($tipo !== null || $ingrediente !== null || $guardadas)
                <a href="{{ route('recetas.index') }}">Limpiar</a>
            @endif
        </div>
    </form>

    @if ($recetas->isEmpty())
        <p class="text-body-secondary">No hay recetas para mostrar.</p>
    @else
        <ul class="list-group mb-3">
            @foreach ($recetas as $receta)
                <li class="list-group-item d-flex flex-wrap gap-2 justify-content-between">
                    <a href="{{ route('recetas.show', $receta) }}" class="fw-semibold text-break">{{ $receta->titulo }}</a>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="badge text-bg-light text-wrap text-break">{{ $receta->tipo }}</span>
                        @if ($receta->guardado_exists)
                            <span class="badge text-bg-success">Guardada</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    {{ $recetas->links('pagination::bootstrap-5') }}

    <form method="POST" action="{{ route('recetas.store') }}" class="card card-body mt-4">
        @csrf
        <h2 class="h5 mb-3">Publicar receta</h2>
        <div class="row g-3">
            <div class="col-md-7">
                <label for="titulo-receta" class="form-label">Título</label>
                <input id="titulo-receta" name="titulo" value="{{ is_scalar(old('titulo')) ? old('titulo') : '' }}"
                       class="form-control @error('titulo') is-invalid @enderror" maxlength="160" required>
            </div>
            <div class="col-md-5">
                <label for="tipo-receta" class="form-label">Tipo de comida</label>
                <input id="tipo-receta" name="tipo" list="tipos-receta" value="{{ is_scalar(old('tipo')) ? old('tipo') : '' }}"
                       class="form-control @error('tipo') is-invalid @enderror" maxlength="80" required>
                <datalist id="tipos-receta">
                    @foreach ($tipos as $opcion)
                        <option value="{{ $opcion }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-12">
                <label for="ingredientes-receta" class="form-label">Ingredientes</label>
                <textarea id="ingredientes-receta" name="ingredientes" maxlength="5000" rows="4"
                          class="form-control @error('ingredientes') is-invalid @enderror"
                          aria-describedby="formato-ingredientes" required>{{ is_scalar(old('ingredientes')) ? old('ingredientes') : '' }}</textarea>
                <p id="formato-ingredientes" class="form-text mb-0">Escribe un ingrediente por línea.</p>
            </div>
            <div class="col-12">
                <label for="preparacion-receta" class="form-label">Preparación</label>
                <textarea id="preparacion-receta" name="preparacion" maxlength="10000" rows="5"
                          class="form-control @error('preparacion') is-invalid @enderror" required>{{ is_scalar(old('preparacion')) ? old('preparacion') : '' }}</textarea>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Publicar receta</button>
        </div>
    </form>
@endsection
