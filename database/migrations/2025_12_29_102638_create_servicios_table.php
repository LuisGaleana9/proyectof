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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id('id_servicio');

            // Llaves foraneas
            $table->unsignedBigInteger('id_alumno');
            $table->foreign('id_alumno')->references('id_usuario')->on('usuarios');

            $table->unsignedBigInteger('id_dependencia');
            $table->foreign('id_dependencia')->references('id_dependencia')->on('dependencias');

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('estado'); // Activo, En pausa, Finalizado   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
