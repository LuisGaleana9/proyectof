<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AlumnoServicio extends Model
{
    // Inscripcion de un alumno a un servicio social
    protected $table = 'alumno_servicio';

    protected $fillable = [
        'id_alumno',
        'id_servicio',
        'tipo_servicio',
        'fecha_inicio',
        'fecha_fin',
        'estado_servicio',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    // Alumno inscrito
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_alumno', 'id_usuario');
    }

    // Servicio al que esta inscrito
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }

    // Actividades individuales asignadas a este alumno en este servicio
    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'id_alumno_servicio', 'id');
    }

    // Registros de horas de este alumno
    public function horas(): HasMany
    {
        return $this->hasMany(Hora::class, 'id_alumno_servicio', 'id');
    }

    // Reportes bimestrales y general del alumno
    public function reportes(): HasMany
    {
        return $this->hasMany(Reporte::class, 'id_alumno_servicio', 'id');
    }

    /**
     * Crear los 4 reportes programados para un alumno Regular.
     * Reportes 1-3: Bimestrales 
     * Reporte 4: General 
     */
    public function crearReportesProgramados(): void
    {
        if ($this->tipo_servicio !== 'Regular') {
            return;
        }

        $fechaInicio = Carbon::parse($this->fecha_inicio);

        // Reportes bimestrales
        for ($i = 1; $i <= 3; $i++) {
            Reporte::create([
                'id_alumno_servicio' => $this->id,
                'numero_reporte' => $i,
                'tipo' => 'Bimestral',
                'fecha_entrega' => $fechaInicio->copy()->addMonths($i * 2),
                'estado' => 'Pendiente',
            ]);
        }

        // Reporte general
        Reporte::create([
            'id_alumno_servicio' => $this->id,
            'numero_reporte' => 4,
            'tipo' => 'General',
            'fecha_entrega' => $fechaInicio->copy()->addMonths(6),
            'estado' => 'Pendiente',
        ]);
    }
}
