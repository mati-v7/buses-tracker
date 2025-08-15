<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('raw_positions', function (Blueprint $table) {
            $table->id();
            $table->string('linea', 10);
            $table->integer('unidad');
            $table->magellanPoint('posicion');
            $table->string('estado', 50)->nullable();
            $table->string('sen', 10)->nullable();
            $table->time('hora_api')->nullable();
            $table->boolean('online')->default(false);
            $table->boolean('aire')->default(false);
            $table->timestamp('capturado_en');
            $table->timestamps();

            $table->index('capturado_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_positions');
    }
};
