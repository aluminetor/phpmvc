@extends('layouts.app')

@section('title', 'Calculadora de propinas')

@section('content')
    @php
        $monto = old('monto', $datos['monto'] ?? '');
        $porcentaje = old('porcentaje', $datos['porcentaje'] ?? '10');
    @endphp

    <h1 class="mb-4">Calculadora de propinas</h1>

    <form method="POST" action="{{ route('propinas.calcular') }}" class="card card-body mb-4">
        @csrf
        <div class="row g-3">
            <div class="col-sm-6">
                <label for="monto" class="form-label">Monto de la cuenta</label>
                <input id="monto" name="monto" type="number" inputmode="decimal" step="0.01"
                       min="0.01" max="999999999.99" value="{{ is_scalar($monto) ? $monto : '' }}"
                       class="form-control @error('monto') is-invalid @enderror" required>
            </div>
            <div class="col-sm-6">
                <label for="porcentaje" class="form-label">Porcentaje de propina (%)</label>
                <input id="porcentaje" name="porcentaje" type="number" inputmode="decimal" step="0.01"
                       min="0" max="100" value="{{ is_scalar($porcentaje) ? $porcentaje : '' }}"
                       class="form-control @error('porcentaje') is-invalid @enderror" required>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Calcular</button>
        </div>
    </form>

    @isset($resultado)
        <section class="card card-body" aria-labelledby="resultado">
            <h2 id="resultado" class="h5 mb-3">Resultado</h2>
            <dl class="row mb-0">
                <dt class="col-7 fw-normal">Monto de la cuenta</dt>
                <dd class="col-5 text-end">{{ number_format($resultado->montoEnCentavos / 100, 2, ',', '.') }}</dd>
                <dt class="col-7 fw-normal">Propina</dt>
                <dd class="col-5 text-end">{{ number_format($resultado->propinaEnCentavos / 100, 2, ',', '.') }}</dd>
                <dt class="col-7">Total a pagar</dt>
                <dd class="col-5 text-end fw-bold mb-0">{{ number_format($resultado->totalEnCentavos / 100, 2, ',', '.') }}</dd>
            </dl>
        </section>
    @endisset
@endsection
