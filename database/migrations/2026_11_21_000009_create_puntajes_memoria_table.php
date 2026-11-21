<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puntajes_memoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partida_id')->unique()->constrained('partidas_memoria')->cascadeOnDelete();
            $table->string('nombre', 80);
            $table->string('dificultad', 10);
            $table->unsignedInteger('intentos');
            $table->unsignedInteger('segundos');
            $table->timestamps();

            $table->index(['dificultad', 'intentos', 'segundos']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puntajes_memoria');
    }
};
