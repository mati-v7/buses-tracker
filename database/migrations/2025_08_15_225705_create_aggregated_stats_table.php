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
        Schema::create('aggregated_stats', function (Blueprint $table) {
            $table->id();
            $table->string('linea', 10);
            $table->date('fecha');
            $table->smallInteger('hora');
            $table->integer('buses_activos')->default(0);
            $table->decimal('promedio_velocidad', 5, 2)->nullable();
            $table->decimal('porcentaje_aire', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['linea', 'fecha', 'hora']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aggregated_stats');
    }
};
