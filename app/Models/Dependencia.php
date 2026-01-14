<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    protected $table = 'dependencias';
    protected $primaryKey = 'id_dependencia';

    protected $fillable = ['nombre', 'id_profesor_responsable'];

    // Una dependencia pertenece a un profesor
    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'id_profesor_responsable', 'id_usuario');
    }
}