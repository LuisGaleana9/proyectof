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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); // Llave primaria
            $table->string('nombre');
            $table->string('apellidos_p');
            $table->string('apellidos_m')->nullable();
            $table->string('matricula')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('rol'); // Alumno, Profesor, Admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
