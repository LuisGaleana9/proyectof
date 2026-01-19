<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Actividad;
use App\Models\Servicio;

class ActividadProfesorController extends Controller
{
    // Ver actividades de un alumno específico (del profesor)
    public function alumno($alumnoId)
    {
        $serviciosIds = \App\Models\Servicio::where('id_profesor_asesor', Auth::id())
            ->where('id_alumno', $alumnoId)
            ->pluck('id_servicio');

        $actividades = Actividad::with(['servicio', 'horas'])
            ->whereIn('id_servicio', $serviciosIds)
            ->orderBy('id_actividad', 'desc')
            ->get();

        return view('profesor.alumnos.actividades.index', compact('actividades', 'alumnoId'));
    }

    // Formulario para crear actividad a un alumno
    public function crear($alumnoId)
    {
        $servicios = \App\Models\Servicio::where('id_profesor_asesor', Auth::id())
            ->where('id_alumno', $alumnoId)
            ->where('estado_servicio', 'Activo')
            ->get();

        return view('profesor.alumnos.actividades.crear', compact('servicios', 'alumnoId'));
    }

    // Guardar actividad
    public function guardar($alumnoId)
    {
        request()->validate([
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'actividad' => 'required|string',
            'fecha_limite' => 'required|date',
            'comentarios' => 'nullable|string',
        ]);

        // Verificar que el servicio pertenece al profesor y al alumno indicado
        $servicio = \App\Models\Servicio::where('id_servicio', request('id_servicio'))
            ->where('id_profesor_asesor', Auth::id())
            ->where('id_alumno', $alumnoId)
            ->firstOrFail();

        Actividad::create([
            'id_servicio' => $servicio->id_servicio,
            'id_alumno' => $alumnoId,
            'actividad' => request('actividad'),
            'comentarios' => request('comentarios'),
            'fecha_limite' => request('fecha_limite'),
            'estado' => 'Activa',
        ]);

        return redirect()->route('profesor.alumnos.actividades.index', $alumnoId)
            ->with('status', 'Actividad asignada.');
    }
    // Listar actividades pendientes de revisión
    public function index()
    {
        $serviciosIds = Servicio::where('id_profesor_asesor', Auth::id())->pluck('id_servicio');

        $actividades = Actividad::with(['servicio'])
            ->whereIn('id_servicio', $serviciosIds)
            ->where('estado', 'Activa')
            ->orderBy('id_actividad', 'desc')
            ->get();

        return view('profesor.actividades.index', compact('actividades'));
    }

    public function aprobar($id)
    {
        $actividad = $this->findOwnedActividad($id);

        // Sólo aprobar si no hay jornada abierta (para Adelantando)
        $abierta = \App\Models\Hora::where('id_actividad', $actividad->id_actividad)
            ->whereNull('hora_final')
            ->first();
        if ($abierta) {
            return back()->withErrors(['actividad' => 'No puedes aprobar mientras hay una jornada en curso.']);
        }

        $actividad->update(['estado' => 'Aprobada']);
        return back()->with('status', 'Actividad aprobada.');
    }

    public function rechazar($id)
    {
        $actividad = $this->findOwnedActividad($id);
        $actividad->update(['estado' => 'Rechazada']);
        return back()->with('status', 'Actividad rechazada.');
    }

    // Regresar actividad a en progreso
    public function regresar($id)
    {
        $actividad = $this->findOwnedActividad($id);
        $actividad->update(['estado' => 'Activa']);
        return back()->with('status', 'Actividad regresada a en progreso.');
    }

    // Cancelar actividad antes de que el alumno trabaje en ella (sin horas registradas)
    public function cancelar($id)
    {
        $actividad = $this->findOwnedActividad($id);
        $tieneHoras = \App\Models\Hora::where('id_actividad', $actividad->id_actividad)->exists();
        if ($tieneHoras) {
            return back()->withErrors(['actividad' => 'No puedes cancelar una actividad que ya tiene horas registradas.']);
        }
        $actividad->update(['estado' => 'Rechazada']);
        return back()->with('status', 'Actividad cancelada.');
    }

    private function findOwnedActividad($id): Actividad
    {
        $actividad = Actividad::with('servicio')
            ->where('id_actividad', $id)
            ->firstOrFail();

        if ($actividad->servicio->id_profesor_asesor !== Auth::id()) {
            abort(403);
        }

        return $actividad;
    }
}
