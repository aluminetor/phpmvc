<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opciones_encuesta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained()->cascadeOnDelete();
            $table->string('texto', 120);
            $table->timestamps();

            $table->unique(['encuesta_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opciones_encuesta');
    }
};
