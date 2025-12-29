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
        Schema::create('horas', function (Blueprint $table) {
            $table->id('id_horas');

            // Registro de entrada y saida
            $table->dateTime('hora_inicio');
            $table->dateTime('hora_final')->nullable();

            $table->enum('asistencia', ['Aprobada', 'Rechazada'])
                ->default('Aprobada');

            // Relacion con Actividad
            $table->unsignedBigInteger('id_actividad');
            $table->foreign('id_actividad')
                ->references('id_actividad')
                ->on('actividades')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horas');
    }
};
