<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hora extends Model
{
    protected $table = 'horas';
    protected $primaryKey = 'id_horas';

    protected $fillable = [
        'hora_inicio',
        'hora_final',
        'asistencia',
        'id_actividad',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'id_actividad', 'id_actividad');
    }
}
