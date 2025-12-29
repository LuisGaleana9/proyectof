<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->id('id_periodo');

            // Fechas de inicio y fin del periodo
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            // Relacion con tabla servicio
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodos');
    }
};
