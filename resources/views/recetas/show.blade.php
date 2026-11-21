@extends('layouts.app')

@section('title', $receta->titulo)

@section('content')
    <a href="{{ route('recetas.index') }}" class="d-inline-block mb-3">Volver a recetas</a>
    <article class="card card-body mb-3">
        <h1 class="text-break">{{ $receta->titulo }}</h1>
        <p><span class="badge text-bg-light text-wrap text-break">{{ $receta->tipo }}</span></p>

        <h2 class="h5">Ingredientes</h2>
        <ul class="text-break">
            @foreach ($ingredientes as $ingrediente)
                <li>{{ $ingrediente }}</li>
            @endforeach
        </ul>

        <h2 class="h5">Preparación</h2>
        <p class="text-break mb-0" style="white-space: pre-wrap;">{{ $receta->preparacion }}</p>
    </article>

    <div class="row g-3 align-items-end">
        <div class="col-md">
            <label for="enlace-receta" class="form-label">Enlace para compartir</label>
            <input id="enlace-receta" class="form-control" value="{{ route('recetas.show', $receta) }}" readonly>
        </div>
        <div class="col-md-auto">
            @if ($receta->guardado_exists)
                <form method="POST" action="{{ route('recetas.quitar-guardado', $receta) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Quitar de guardadas</button>
                </form>
            @else
                <form method="POST" action="{{ route('recetas.guardar', $receta) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Guardar receta</button>
                </form>
            @endif
        </div>
    </div>
@endsection
