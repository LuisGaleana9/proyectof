<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hora extends Model
{
    // Registro de horas trabajadas por actividad
    protected $table = 'horas';
    protected $primaryKey = 'id_horas';

    protected $fillable = [
        'hora_inicio',
        'hora_final',
        'asistencia',
        'id_actividad',
        'horas_totales',
    ];

    public function actividad(): BelongsTo
    {
        // Actividad a la que pertenece el registro
        return $this->belongsTo(Actividad::class, 'id_actividad', 'id_actividad');
    }
}
