<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Hora;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class ProfesorRevisionController extends Controller
{
    // Listar actividades en revision del profesor
    public function index()
    {
        $profesorId = Auth::user()->id_usuario;
        $actividades = Actividad::with(['servicio', 'alumnoServicio.alumno', 'horas'])
            ->where('estado', 'En Revisión')
            ->whereHas('servicio', function ($q) use ($profesorId) {
                $q->where('id_profesor', $profesorId);
            })
            ->orderBy('fecha_limite', 'asc')
            ->get();

        return view('profesor.revisiones', ['actividades' => $actividades]);
    }

    // Rechazar/eliminar registro individual de horas
    public function rechazarHora($idHora)
    {
        $hora = Hora::with('actividad.servicio')->findOrFail($idHora);
        $profesorId = Auth::user()->id_usuario;

        if ($hora->actividad->servicio->id_profesor !== $profesorId) {
            abort(403);
        }

        if ($hora->actividad->estado !== 'En Revisión') {
            return back()->withErrors(['Solo puedes rechazar horas de actividades en revisión.']);
        }

        $hora->delete();

        $actividad = Actividad::with('horas')->findOrFail($hora->id_actividad);

        $totalMinutos = (int) $actividad->horas->reduce(function ($carry, $h) {
            if (!$h->hora_final) {
                return $carry;
            }

            if (Schema::hasColumn('horas', 'horas_totales') && $h->horas_totales !== null) {
                return $carry + (int) round(((float) $h->horas_totales) * 60);
            }

            $inicio = Carbon::parse($h->hora_inicio);
            $fin = Carbon::parse($h->hora_final);
            return $carry + $inicio->diffInMinutes($fin);
        }, 0);

        $horas = intdiv($totalMinutos, 60);
        $minutos = $totalMinutos % 60;

        return back()->with('status', "Registro de horas rechazado y eliminado. Total válido restante: {$horas}h {$minutos}m.");
    }

    // Aprobar actividad
    public function aprobar($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $profesorId = Auth::user()->id_usuario;

        if ($actividad->servicio->id_profesor !== $profesorId) {
            abort(403);
        }

        $actividad->estado = 'Aprobada';
        $actividad->save();

        return back()->with('status', 'Actividad aprobada.');
    }

    // Rechazar actividad
    public function rechazar($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $profesorId = Auth::user()->id_usuario;

        if ($actividad->servicio->id_profesor !== $profesorId) {
            abort(403);
        }

        $actividad->estado = 'Rechazada';
        $actividad->save();

        return back()->with('status', 'Actividad rechazada.');
    }
}
