<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vueltas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cronometro_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->unsignedBigInteger('parcial_ms');
            $table->unsignedBigInteger('total_ms');
            $table->timestamps();

            $table->unique(['cronometro_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vueltas');
    }
};
