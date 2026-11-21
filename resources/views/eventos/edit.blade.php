@extends('layouts.app')

@section('title', 'Editar evento')

@section('content')
    <h1 class="mb-3">Editar evento</h1>
    <a href="{{ route('eventos.index', ['mes' => $evento->inicioLocal()->format('Y-m')]) }}" class="d-inline-block mb-4">Volver al calendario</a>

    <form method="POST" action="{{ route('eventos.update', $evento) }}" class="card card-body mb-3">
        @csrf
        @method('PUT')
        @include('eventos.formulario')
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>

    <form method="POST" action="{{ route('eventos.destroy', $evento) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger">Eliminar evento</button>
    </form>
@endsection
