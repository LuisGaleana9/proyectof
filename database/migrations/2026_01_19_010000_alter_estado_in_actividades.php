<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ajustar el ENUM para agregar 'En Revisión'
        DB::statement("ALTER TABLE actividades MODIFY COLUMN estado ENUM('Aprobada','Activa','Rechazada','En Revisión') DEFAULT 'Activa'");
    }

    public function down(): void
    {
        // Revertir a valores originales
        DB::statement("ALTER TABLE actividades MODIFY COLUMN estado ENUM('Aprobada','Activa','Rechazada') DEFAULT 'Activa'");
    }
};
