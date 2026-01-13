<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
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
        return $this->belongsTo(Usuario::class, 'id_alumno', 'id_usuario');
    }

    public function asesor()
    {
        return $this->belongsTo(Usuario::class, 'id_profesor_asesor', 'id_usuario');
    }

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia', 'id_dependencia');
    }
}
