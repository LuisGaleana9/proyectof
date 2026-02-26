<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory;

    // Usuario (alumno, profesor, admin)
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $fillable = [
        'nombre',
        'apellidos_p',
        'apellidos_m',
        'email',
        'password',
        'rol',
        'matricula',
        'profesor_id',
        'id_dependencia',
    ];

    // Dependencia a la que pertenece el profesor
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia', 'id_dependencia');
    }

    // Profesor que creo a este alumno
    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'profesor_id', 'id_usuario');
    }

    // Alumnos creados por este profesor
    public function alumnos()
    {
        return $this->hasMany(Usuario::class, 'profesor_id', 'id_usuario');
    }

    // Servicios creados por este profesor
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'id_profesor', 'id_usuario');
    }

    // Inscripciones de este alumno a servicios
    public function alumnoServicios()
    {
        return $this->hasMany(AlumnoServicio::class, 'id_alumno', 'id_usuario');
    }
}
