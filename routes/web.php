<?php

use App\Http\Controllers\ContrasenaController;
use App\Http\Controllers\CronometroController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\MemoriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PropinaController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\TareaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tareas')->name('inicio');

Route::prefix('tareas')->name('tareas.')->controller(TareaController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::patch('/{tarea}', 'update')->name('update');
    Route::delete('/{tarea}', 'destroy')->name('destroy');
});

Route::prefix('propinas')->name('propinas.')->controller(PropinaController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'calcular')->name('calcular');
});

Route::prefix('contrasenas')->name('contrasenas.')->controller(ContrasenaController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'generar')->name('generar');
});

Route::prefix('gastos')->name('gastos.')->controller(GastoController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/resumen', 'resumen')->name('resumen');
});

Route::prefix('reservas')->name('reservas.')->controller(ReservaController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/disponibilidad', 'disponibilidad')->name('disponibilidad');
    Route::post('/', 'store')->name('store');
    Route::patch('/{reserva}/confirmacion', 'confirmar')->name('confirmar');
});

Route::prefix('notas')->name('notas.')->controller(NotaController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
});

Route::prefix('eventos')->name('eventos.')->controller(EventoController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{evento}/editar', 'edit')->name('edit');
    Route::put('/{evento}', 'update')->name('update');
    Route::delete('/{evento}', 'destroy')->name('destroy');
});

Route::prefix('recetas')->name('recetas.')->controller(RecetaController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{receta}', 'show')->name('show');
    Route::post('/{receta}/guardado', 'guardar')->name('guardar');
    Route::delete('/{receta}/guardado', 'quitarGuardado')->name('quitar-guardado');
});

Route::prefix('memoria')->name('memoria.')->controller(MemoriaController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/partidas', 'iniciar')->name('iniciar');
    Route::get('/tablero', 'tablero')->name('tablero');
    Route::post('/puntajes', 'guardarPuntaje')->name('puntajes.store');
});

Route::prefix('encuestas')->name('encuestas.')->controller(EncuestaController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{encuesta}', 'show')->name('show');
    Route::post('/{encuesta}/votos', 'votar')->name('votar');
    Route::get('/{encuesta}/resultados', 'resultados')->name('resultados');
});

Route::prefix('cronometro')->name('cronometro.')->controller(CronometroController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/inicio', 'iniciar')->name('iniciar');
    Route::patch('/pausa', 'pausar')->name('pausar');
    Route::delete('/reinicio', 'reiniciar')->name('reiniciar');
    Route::post('/vueltas', 'guardarVuelta')->name('vueltas.store');
});
