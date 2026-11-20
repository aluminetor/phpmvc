<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Ejercicios PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset('css/ejercicios.css') }}" rel="stylesheet">
</head>
<body>
    <a class="visually-hidden-focusable position-absolute p-3 bg-white" href="#contenido">Ir al contenido</a>
    <header class="border-bottom bg-white">
        <div class="container py-3">
            <a class="fw-bold fs-5 text-dark text-decoration-none" href="{{ route('inicio') }}">Ejercicios PHP</a>
        </div>
    </header>

    <div class="container py-4 py-lg-5">
        <div class="row g-4 g-lg-5">
            <aside class="col-lg-3">
                <nav class="module-nav nav flex-lg-column gap-2" aria-label="Módulos">
                    <a class="nav-link" href="{{ route('tareas.index') }}" @if(request()->routeIs('tareas.*')) aria-current="page" @endif>Tareas</a>
                    <a class="nav-link" href="{{ route('propinas.index') }}" @if(request()->routeIs('propinas.*')) aria-current="page" @endif>Propinas</a>
                    <a class="nav-link" href="{{ route('contrasenas.index') }}" @if(request()->routeIs('contrasenas.*')) aria-current="page" @endif>Contraseñas</a>
                    <a class="nav-link" href="{{ route('gastos.index') }}" @if(request()->routeIs('gastos.*')) aria-current="page" @endif>Gastos</a>
                    <a class="nav-link" href="{{ route('reservas.index') }}" @if(request()->routeIs('reservas.*')) aria-current="page" @endif>Reservas</a>
                    <a class="nav-link" href="{{ route('notas.index') }}" @if(request()->routeIs('notas.*')) aria-current="page" @endif>Notas</a>
                    <a class="nav-link" href="{{ route('eventos.index') }}" @if(request()->routeIs('eventos.*')) aria-current="page" @endif>Eventos</a>
                    <a class="nav-link" href="{{ route('recetas.index') }}" @if(request()->routeIs('recetas.*')) aria-current="page" @endif>Recetas</a>
                    <a class="nav-link" href="{{ route('memoria.index') }}" @if(request()->routeIs('memoria.*')) aria-current="page" @endif>Memoria</a>
                    <a class="nav-link" href="{{ route('encuestas.index') }}" @if(request()->routeIs('encuestas.*')) aria-current="page" @endif>Encuestas</a>
                    <a class="nav-link" href="{{ route('cronometro.index') }}" @if(request()->routeIs('cronometro.*')) aria-current="page" @endif>Cronómetro</a>
                </nav>
            </aside>

            <main id="contenido" class="col-lg-9" tabindex="-1">
                @if (session('estado'))
                    <div class="alert alert-success" role="status">{{ session('estado') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <p class="fw-semibold mb-1">Revisa los datos del formulario.</p>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
