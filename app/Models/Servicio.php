<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    // Servicio social creado por el profesor
    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'nombre',
        'descripcion',
        'id_profesor',
    ];

    // Profesor dueno del servicio
    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'id_profesor', 'id_usuario');
    }

    // Inscripciones de alumnos a este servicio
    public function alumnoServicios()
    {
        return $this->hasMany(AlumnoServicio::class, 'id_servicio', 'id_servicio');
    }

    // Actividades asociadas al servicio
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_servicio', 'id_servicio');
    }
}
