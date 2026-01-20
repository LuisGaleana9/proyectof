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
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'profesor_id')) {
                // Agregar columna profesor_id para relacionar alumnos con su profesor creador
                $table->unsignedBigInteger('profesor_id')->nullable()->after('rol');
                $table->foreign('profesor_id')
                    ->references('id_usuario')
                    ->on('usuarios')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['profesor_id']);
            $table->dropColumn('profesor_id');
        });
    }
};
