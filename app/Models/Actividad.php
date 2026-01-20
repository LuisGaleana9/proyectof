<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    // Actividad asignada dentro del servicio social
    protected $table = 'actividades';
    protected $primaryKey = 'id_actividad';

    protected $fillable = [
        'actividad',
        'comentarios',
        'estado',
        'fecha_limite',
        'id_servicio',
        'id_alumno',
    ];

    public function servicio(): BelongsTo
    {
        // Servicio al que pertenece la actividad
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }

    public function horas(): HasMany
    {
        // Registros de horas de la actividad
        return $this->hasMany(Hora::class, 'id_actividad', 'id_actividad');
    }
}
