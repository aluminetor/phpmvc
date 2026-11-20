<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->date('fecha');
            $table->time('hora');
            $table->dateTime('confirmada_en')->nullable();
            $table->timestamps();

            $table->unique(['servicio_id', 'fecha', 'hora']);
            $table->index(['fecha', 'hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
