<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Actividad;
use App\Models\Hora;
use App\Models\Servicio;

class ActividadAlumnoController extends Controller
{
    // Listar actividades del alumno autenticado
    public function index()
    {
        $serviciosIds = Servicio::where('id_alumno', Auth::id())->pluck('id_servicio');

        $actividades = Actividad::with(['horas', 'servicio'])
            ->whereIn('id_servicio', $serviciosIds)
            ->orderBy('id_actividad', 'desc')
            ->get();

        return view('alumno.actividades.index', compact('actividades'));
    }

    // Iniciar conteo de horas (adelantando)
    public function start($id)
    {
        $actividad = $this->findOwnedActividad($id);

        // Verificar tipo de servicio
        $servicio = $actividad->servicio;
        if ($servicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['actividad' => 'Esta actividad no requiere conteo de horas.']);
        }

        // Evitar doble inicio: si existe un registro abierto
        $abierta = Hora::where('id_actividad', $actividad->id_actividad)
            ->whereNull('hora_final')
            ->first();
        if ($abierta) {
            return back()->withErrors(['actividad' => 'Ya tienes una jornada abierta para esta actividad.']);
        }

        // Si el alumno ya marcó como completada (pendiente de confirmación), bloquear nuevos inicios
        $tieneHorasCerradas = Hora::where('id_actividad', $actividad->id_actividad)
            ->whereNotNull('hora_final')
            ->exists();
        if ($actividad->estado === 'Activa' && $tieneHorasCerradas) {
            return back()->withErrors(['actividad' => 'Esta actividad ya fue enviada para confirmación y no puedes iniciar nueva jornada.']);
        }

        Hora::create([
            'id_actividad' => $actividad->id_actividad,
            'hora_inicio' => now(),
        ]);

        return back()->with('status', 'Jornada iniciada.');
    }

    // Finalizar conteo de horas (adelantando)
    public function stop($id)
    {
        $actividad = $this->findOwnedActividad($id);
        $servicio = $actividad->servicio;
        if ($servicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['actividad' => 'Esta actividad no requiere conteo de horas.']);
        }

        $abierta = Hora::where('id_actividad', $actividad->id_actividad)
            ->whereNull('hora_final')
            ->orderBy('id_horas', 'desc')
            ->first();

        if (!$abierta) {
            return back()->withErrors(['actividad' => 'No tienes una jornada abierta para esta actividad.']);
        }

        $abierta->update(['hora_final' => now()]);

        return back()->with('status', 'Jornada finalizada.');
    }

    // Marcar actividad como completada por el alumno (pendiente de revisión)
    public function completar($id)
    {
        $actividad = $this->findOwnedActividad($id);

        // Si hay una jornada abierta, cerrarla
        Hora::where('id_actividad', $actividad->id_actividad)
            ->whereNull('hora_final')
            ->update(['hora_final' => now()]);

        // Para servicios regulares, crear un registro de hora inmediata para marcar intento de finalización
        if ($actividad->servicio->tipo_servicio === 'Regular') {
            // Sólo si aún no hay horas registradas
            if (!$actividad->horas()->exists()) {
                Hora::create([
                    'id_actividad' => $actividad->id_actividad,
                    'hora_inicio' => now(),
                    'hora_final' => now(),
                    'asistencia' => 'Aprobada',
                ]);
            }
        }

        // Estado permanece 'Activa' como pendiente hasta confirmación del profesor
        $actividad->update(['estado' => 'Activa']);

        return back()->with('status', 'Actividad enviada para revisión del profesor.');
    }

    // Informe: lista títulos de actividades aprobadas
    public function informe()
    {
        $serviciosIds = Servicio::where('id_alumno', Auth::id())->pluck('id_servicio');

        $actividades = Actividad::whereIn('id_servicio', $serviciosIds)
            ->where('estado', 'Aprobada')
            ->orderBy('id_actividad', 'desc')
            ->get(['id_actividad', 'actividad']);

        return view('alumno.actividades.informe', compact('actividades'));
    }

    private function findOwnedActividad($id): Actividad
    {
        $actividad = Actividad::with('servicio')
            ->where('id_actividad', $id)
            ->firstOrFail();

        if ($actividad->servicio->id_alumno !== Auth::id()) {
            abort(403);
        }

        return $actividad;
    }
}
