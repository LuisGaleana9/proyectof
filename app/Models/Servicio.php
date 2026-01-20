<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    // Servicio social asignado al alumno
    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'id_alumno',
        'id_profesor_asesor',
        'id_dependencia',
        'tipo_servicio',
        'fecha_inicio',
        'fecha_fin',
        'estado_servicio'
    ];

    public function alumno()
    {
        // Alumno asignado al servicio
        return $this->belongsTo(Usuario::class, 'id_alumno', 'id_usuario');
    }

    public function asesor()
    {
        // Profesor asesor del servicio
        return $this->belongsTo(Usuario::class, 'id_profesor_asesor', 'id_usuario');
    }

    public function dependencia()
    {
        // Dependencia responsable
        return $this->belongsTo(Dependencia::class, 'id_dependencia', 'id_dependencia');
    }

    public function actividades()
    {
        // Actividades asociadas al servicio
        return $this->hasMany(Actividad::class, 'id_servicio', 'id_servicio');
    }
}
