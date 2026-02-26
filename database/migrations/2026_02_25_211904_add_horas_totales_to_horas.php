<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('horas', function (Blueprint $table) {
            $table->decimal('horas_totales', 8, 2)->nullable()->after('hora_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horas', function (Blueprint $table) {
            $table->dropColumn('horas_totales');
        });
    }
};
