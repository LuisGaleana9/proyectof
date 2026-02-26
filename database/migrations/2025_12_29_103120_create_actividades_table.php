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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id('id_actividad');

            $table->text('actividad');
            $table->text('comentarios')->nullable();

            $table->enum('estado', ['Aprobada', 'Activa', 'Rechazada'])
                ->default('Activa');

            $table->date('fecha_limite');

            // Relacion con Servicio
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->onDelete('cascade');

            // Actividad individual (si es null, es grupal para todo el servicio)
            $table->unsignedBigInteger('id_alumno_servicio')->nullable();
            $table->foreign('id_alumno_servicio')
                ->references('id')
                ->on('alumno_servicio')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('actividades');
    }
};
