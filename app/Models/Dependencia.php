<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    // Dependencia receptora del servicio social
    protected $table = 'dependencias';
    protected $primaryKey = 'id_dependencia';

    protected $fillable = ['nombre'];

    // Profesores adscritos a esta dependencia
    public function profesores()
    {
        return $this->hasMany(Usuario::class, 'id_dependencia', 'id_dependencia');
    }
}