@extends('layouts.app')

@section('title', 'Resumen mensual de gastos')

@section('content')
    <h1 class="mb-3">Resumen mensual de gastos</h1>
    <a href="{{ route('gastos.index') }}" class="d-inline-block mb-4">Volver a gastos</a>

    <form method="GET" action="{{ route('gastos.resumen') }}" class="card card-body mb-4">
        @include('gastos.filtro-mes')
    </form>

    <section class="card card-body" aria-labelledby="total-gastos">
        <h2 id="total-gastos" class="h5 mb-2">Total del mes</h2>
        <p class="fs-3 fw-bold mb-3">{{ number_format($total, 2, ',', '.') }}</p>

        @if ($categorias->isEmpty())
            <p class="mb-0 text-body-secondary">No hay gastos para el mes seleccionado.</p>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <caption class="caption-top">Distribución por categoría</caption>
                    <thead>
                        <tr>
                            <th scope="col">Categoría</th>
                            <th scope="col" class="text-end">Gastos</th>
                            <th scope="col" class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorias as $categoria)
                            <tr>
                                <th scope="row" class="text-break">{{ $categoria->categoria }}</th>
                                <td class="text-end">{{ $categoria->cantidad }}</td>
                                <td class="text-end text-nowrap">{{ number_format($categoria->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
