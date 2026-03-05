<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Reporte extends Model
{
    protected $table = 'reportes';

    protected $fillable = [
        'id_alumno_servicio',
        'numero_reporte',
        'tipo',
        'contenido',
        'fecha_entrega',
        'estado',
        'correcciones',
        'fecha_envio',
        'fecha_revision',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'fecha_envio' => 'datetime',
        'fecha_revision' => 'datetime',
    ];

    public function alumnoServicio(): BelongsTo
    {
        return $this->belongsTo(AlumnoServicio::class, 'id_alumno_servicio', 'id');
    }

    /**
     * Determina si el alumno puede escribir/editar este reporte.
     * 1. Estado es Pendiente o Corregir
     * 2. Estamos dentro de la ventana de 10 dias antes de fecha_entrega
     * 3. El reporte anterior esta aprobado
     */
    public function puedeEscribir(): bool
    {
        // Solo se puede escribir si esta pendiente o necesita correcciones
        if (!in_array($this->estado, ['Pendiente', 'Corregir'])) {
            return false;
        }

        // Verificar ventana de 10 dias antes de la fecha de entrega
        $hoy = Carbon::today();
        $fechaApertura = $this->fecha_entrega->copy()->subDays(10);

        if ($hoy->lt($fechaApertura)) {
            return false;
        }

        // Se permite entregar 3 dias despues de la fecha
        if ($hoy->gt($this->fecha_entrega->copy()->addDays(3))) {
            return false;
        }

        // Verificar que el reporte anterior este aprobado
        if (!$this->reporteAnteriorAprobado()) {
            return false;
        }

        return true;
    }

    // Verificar que el reporte anterior en secuencia este aprobado
    public function reporteAnteriorAprobado(): bool
    {
        if ($this->numero_reporte === 1) {
            return true;
        }

        $numeroAnterior = $this->numero_reporte - 1;

        $anterior = Reporte::where('id_alumno_servicio', $this->id_alumno_servicio)
            ->where('numero_reporte', $numeroAnterior)
            ->first();

        return $anterior && $anterior->estado === 'Aprobado';
    }

    // Fecha de apertura para escribir un reporte
    public function fechaApertura(): Carbon
    {
        return $this->fecha_entrega->copy()->subDays(10);
    }

    public function nombreReporte(): string
    {
        if ($this->tipo === 'General') {
            return 'Reporte General de Servicio Social';
        }
        return 'Reporte Bimestral #' . $this->numero_reporte;
    }
}
