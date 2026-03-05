<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AlumnoServicio;

class CrearReportesExistentes extends Command
{
    protected $signature = 'reportes:backfill';
    protected $description = 'Crear reportes programados para alumnos Regular existentes que no tienen reportes';

    public function handle()
    {
        $inscripciones = AlumnoServicio::where('tipo_servicio', 'Regular')
            ->whereDoesntHave('reportes')
            ->get();

        if ($inscripciones->isEmpty()) {
            $this->info('No hay inscripciones Regular sin reportes.');
            return 0;
        }

        $count = 0;
        foreach ($inscripciones as $inscripcion) {
            $inscripcion->crearReportesProgramados();
            $count++;
            $this->line("Reportes creados para alumno_servicio #{$inscripcion->id} (alumno: {$inscripcion->id_alumno})");
        }

        $this->info("Se crearon reportes para {$count} inscripciones.");
        return 0;
    }
}
