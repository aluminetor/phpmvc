@extends('layouts.app')

@section('title', 'Generador de contraseñas')

@section('content')
    @php
        $longitud = old('longitud', $datos['longitud'] ?? 16);
        $tiposSeleccionados = (array) old('tipos', $datos['tipos'] ?? ($errors->any() ? [] : ['mayusculas', 'minusculas', 'numeros', 'simbolos']));
    @endphp

    <h1 class="mb-4">Generador de contraseñas</h1>

    <form method="POST" action="{{ route('contrasenas.generar') }}" class="card card-body mb-4">
        @csrf
        <div class="mb-3">
            <label for="longitud" class="form-label">Longitud</label>
            <input id="longitud" name="longitud" type="number" min="12" max="128"
                   value="{{ is_scalar($longitud) ? $longitud : '' }}" aria-describedby="rango-longitud"
                   class="form-control @error('longitud') is-invalid @enderror" required>
            <div id="rango-longitud" class="form-text">De 12 a 128 caracteres.</div>
        </div>
        <fieldset>
            <legend class="fs-6 fw-semibold">Tipos de caracteres</legend>
            @foreach (['mayusculas' => 'Mayúsculas', 'minusculas' => 'Minúsculas', 'numeros' => 'Números', 'simbolos' => 'Símbolos'] as $tipo => $etiqueta)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="tipos[]" id="{{ $tipo }}"
                           value="{{ $tipo }}" @checked(in_array($tipo, $tiposSeleccionados, true))>
                    <label class="form-check-label" for="{{ $tipo }}">{{ $etiqueta }}</label>
                </div>
            @endforeach
        </fieldset>
        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Generar contraseña</button>
        </div>
    </form>

    @isset($contrasena)
        <section class="card card-body">
            <label for="contrasena" class="form-label fw-semibold">Contraseña generada</label>
            <input id="contrasena" class="form-control font-monospace" type="text"
                   value="{{ $contrasena }}" readonly spellcheck="false" autocomplete="off" dir="ltr">
        </section>
    @endisset
@endsection
