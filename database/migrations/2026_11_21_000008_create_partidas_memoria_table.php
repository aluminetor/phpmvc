<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidas_memoria', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('dificultad', 10);
            $table->json('mazo');
            $table->json('seleccion');
            $table->json('encontradas');
            $table->unsignedInteger('intentos')->default(0);
            $table->unsignedInteger('turno')->default(0);
            $table->dateTime('terminada_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas_memoria');
    }
};
