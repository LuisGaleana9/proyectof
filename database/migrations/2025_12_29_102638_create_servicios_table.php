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

            $table->string('nombre');
            $table->text('descripcion')->nullable();

            // Profesor dueño del servicio
            $table->unsignedBigInteger('id_profesor');
            $table->foreign('id_profesor')->references('id_usuario')->on('usuarios')->onDelete('cascade');

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
