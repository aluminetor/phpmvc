@extends('layouts.app')

@section('title', 'Gestor de gastos')

@section('content')
    <h1 class="mb-4">Gestor de gastos</h1>

    <form method="POST" action="{{ route('gastos.store') }}" class="card card-body mb-4">
        @csrf
        <h2 class="h5 mb-3">Registrar gasto</h2>
        <div class="row g-3">
            <div class="col-sm-6">
                <label for="concepto" class="form-label">Concepto</label>
                <input id="concepto" name="concepto" class="form-control @error('concepto') is-invalid @enderror"
                       value="{{ is_scalar(old('concepto')) ? old('concepto') : '' }}" maxlength="160" required>
            </div>
            <div class="col-sm-6">
                <label for="categoria" class="form-label">Categoría</label>
                <input id="categoria" name="categoria" list="categorias-gasto"
                       class="form-control @error('categoria') is-invalid @enderror"
                       value="{{ is_scalar(old('categoria')) ? old('categoria') : '' }}" maxlength="80" required>
                <datalist id="categorias-gasto">
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-sm-6">
                <label for="monto" class="form-label">Monto</label>
                <input id="monto" name="monto" inputmode="decimal"
                       class="form-control @error('monto') is-invalid @enderror"
                       value="{{ is_scalar(old('monto')) ? old('monto') : '' }}" maxlength="12" required>
            </div>
            <div class="col-sm-6">
                <label for="fecha-gasto" class="form-label">Fecha</label>
                <input id="fecha-gasto" name="fecha" type="date" min="1000-01-01" max="9999-12-31"
                       class="form-control @error('fecha') is-invalid @enderror"
                       value="{{ is_scalar(old('fecha', $hoy->toDateString())) ? old('fecha', $hoy->toDateString()) : '' }}" required>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Registrar gasto</button>
        </div>
    </form>

    <form method="GET" action="{{ route('gastos.resumen') }}" class="card card-body mb-4">
        @include('gastos.filtro-mes', ['mes' => $hoy->format('Y-m')])
    </form>

    <h2 class="h5 mb-3">Gastos registrados</h2>
    @if ($gastos->isEmpty())
        <p class="text-body-secondary">Aún no hay gastos registrados.</p>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col">Fecha</th>
                        <th scope="col">Concepto</th>
                        <th scope="col">Categoría</th>
                        <th scope="col" class="text-end">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gastos as $gasto)
                        <tr>
                            <td class="text-nowrap">{{ $gasto->fecha->format('d/m/Y') }}</td>
                            <td class="text-break">{{ $gasto->concepto }}</td>
                            <td class="text-break">{{ $gasto->categoria }}</td>
                            <td class="text-end text-nowrap">{{ number_format($gasto->monto, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{ $gastos->links('pagination::bootstrap-5') }}
@endsection
