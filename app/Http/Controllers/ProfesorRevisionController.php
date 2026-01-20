<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;

class ProfesorRevisionController extends Controller
{
    // Listar actividades en revision del profesor
    public function index()
    {
        $profesorId = Auth::user()->id_usuario;
        $actividades = Actividad::with('servicio')
            ->where('estado', 'En Revisión')
            ->whereHas('servicio', function ($q) use ($profesorId) {
                $q->where('id_profesor_asesor', $profesorId);
            })
            ->orderBy('fecha_limite', 'asc')
            ->get();

        return view('profesor.revisiones', ['actividades' => $actividades]);
    }

    // Aprobar actividad
    public function aprobar($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $profesorId = Auth::user()->id_usuario;

        if ($actividad->servicio->id_profesor_asesor !== $profesorId) {
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

        if ($actividad->servicio->id_profesor_asesor !== $profesorId) {
            abort(403);
        }

        $actividad->estado = 'Rechazada';
        $actividad->save();

        return back()->with('status', 'Actividad rechazada.');
    }
}
