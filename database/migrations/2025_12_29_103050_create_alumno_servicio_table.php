<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alumno_servicio', function (Blueprint $table) {
            $table->id();

            // Alumno inscrito en el servicio
            $table->unsignedBigInteger('id_alumno');
            $table->foreign('id_alumno')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            // Servicio al que se inscribe
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');

            // Datos del servicio social del alumno
            $table->enum('tipo_servicio', ['Regular', 'Adelantando']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('estado_servicio', ['Activo', 'En pausa', 'Finalizado'])->default('Activo');

            // Un alumno no puede estar inscrito dos veces en el mismo servicio
            $table->unique(['id_alumno', 'id_servicio']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumno_servicio');
    }
};
