<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votos_encuesta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id');
            $table->foreignId('opcion_encuesta_id')->index();
            $table->uuid('navegador_id');
            $table->timestamps();

            $table->unique(['encuesta_id', 'navegador_id']);
            $table->foreign(['encuesta_id', 'opcion_encuesta_id'], 'votos_encuesta_opcion_fk')
                ->references(['encuesta_id', 'id'])->on('opciones_encuesta')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votos_encuesta');
    }
};
