<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    protected $table = 'actividades';
    protected $primaryKey = 'id_actividad';

    protected $fillable = [
        'actividad',
        'comentarios',
        'estado',
        'fecha_limite',
        'id_alumno',
        'id_servicio',
    ];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }

    public function horas(): HasMany
    {
        return $this->hasMany(Hora::class, 'id_actividad', 'id_actividad');
    }
}
