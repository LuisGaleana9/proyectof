<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();

            // Inscripcion del alumno al servicio
            $table->unsignedBigInteger('id_alumno_servicio');
            $table->foreign('id_alumno_servicio')
                ->references('id')->on('alumno_servicio')
                ->onDelete('cascade');

            $table->tinyInteger('numero_reporte');
            $table->enum('tipo', ['Bimestral', 'General']);
            $table->text('contenido')->nullable();
            $table->date('fecha_entrega');
            $table->enum('estado', ['Pendiente', 'Enviado', 'Aprobado', 'Rechazado', 'Corregir'])
                ->default('Pendiente');
            $table->text('correcciones')->nullable();
            $table->datetime('fecha_envio')->nullable();
            $table->datetime('fecha_revision')->nullable();
            $table->timestamps();
            $table->unique(['id_alumno_servicio', 'numero_reporte']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
