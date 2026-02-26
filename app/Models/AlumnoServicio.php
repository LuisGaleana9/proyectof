<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
