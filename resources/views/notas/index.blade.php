@extends('layouts.app')

@section('title', 'Gestor de notas')

@section('content')
    <h1 class="mb-4">Gestor de notas</h1>

    <form method="POST" action="{{ route('notas.store') }}" class="card card-body mb-4">
        @csrf
        <h2 class="h5 mb-3">Nueva nota</h2>
        <div class="row g-3">
            <div class="col-sm-7">
                <label for="titulo-nota" class="form-label">Título</label>
                <input id="titulo-nota" name="titulo" maxlength="160"
                       class="form-control @error('titulo') is-invalid @enderror"
                       value="{{ is_scalar(old('titulo')) ? old('titulo') : '' }}" required>
            </div>
            <div class="col-sm-5">
                <label for="categoria-nota" class="form-label">Categoría</label>
                <input id="categoria-nota" name="categoria" list="categorias-nota" maxlength="80"
                       class="form-control @error('categoria') is-invalid @enderror"
                       value="{{ is_scalar(old('categoria')) ? old('categoria') : '' }}" required>
                <datalist id="categorias-nota">
                    @foreach ($categorias as $opcion)
                        <option value="{{ $opcion }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-12">
                <label for="contenido-nota" class="form-label">Contenido</label>
                <textarea id="contenido-nota" name="contenido" rows="4" maxlength="10000"
                          class="form-control @error('contenido') is-invalid @enderror" required>{{ is_scalar(old('contenido')) ? old('contenido') : '' }}</textarea>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Guardar nota</button>
        </div>
    </form>

    <section aria-labelledby="notas-guardadas">
        <h2 id="notas-guardadas" class="h5 mb-3">Notas guardadas</h2>
        <form method="GET" action="{{ route('notas.index') }}" class="card card-body mb-3">
            <div class="row g-3 align-items-end">
                <div class="col-sm">
                    <label for="buscar-nota" class="form-label">Palabra clave</label>
                    <input id="buscar-nota" name="q" type="search" class="form-control"
                           value="{{ $palabra }}" maxlength="160">
                </div>
                <div class="col-sm">
                    <label for="filtrar-categoria" class="form-label">Filtrar por categoría</label>
                    <input id="filtrar-categoria" name="categoria" class="form-control" list="categorias-nota"
                           value="{{ $categoria }}" maxlength="80">
                </div>
                <div class="col-sm-auto d-flex flex-wrap align-items-center gap-2">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                    @if ($palabra !== null || $categoria !== null)
                        <a href="{{ route('notas.index') }}">Limpiar</a>
                    @endif
                </div>
            </div>
        </form>

        @forelse ($notas as $nota)
            <article class="card card-body mb-3">
                <div class="d-flex flex-wrap gap-2 align-items-start justify-content-between mb-2">
                    <h3 class="h6 mb-0 text-break">{{ $nota->titulo }}</h3>
                    <span class="badge text-bg-light text-wrap text-break">{{ $nota->categoria }}</span>
                </div>
                <div class="mb-0 text-break">{!! nl2br(e($nota->contenido)) !!}</div>
            </article>
        @empty
            <p class="text-body-secondary">
                {{ $palabra !== null || $categoria !== null ? 'No hay notas que coincidan con la búsqueda.' : 'Aún no hay notas guardadas.' }}
            </p>
        @endforelse

        {{ $notas->links('pagination::bootstrap-5') }}
    </section>
@endsection
