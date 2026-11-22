<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cronometros', function (Blueprint $table) {
            $table->id();
            $table->uuid('navegador_id')->unique();
            $table->unsignedBigInteger('acumulado_ms')->default(0);
            $table->unsignedBigInteger('iniciado_en_ms')->nullable();
            $table->unsignedBigInteger('version')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cronometros');
    }
};
