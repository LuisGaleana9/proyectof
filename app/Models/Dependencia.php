<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    // Dependencia receptora del servicio social
    protected $table = 'dependencias';
    protected $primaryKey = 'id_dependencia';

    protected $fillable = ['nombre', 'id_profesor_responsable'];

    // Profesor responsable de la dependencia
    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'id_profesor_responsable', 'id_usuario');
    }
}