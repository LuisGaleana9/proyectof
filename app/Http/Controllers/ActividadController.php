<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Actividad;
use App\Models\Hora;

class ActividadController extends Controller
{
    public function show($id)
    {
        $actividad = Actividad::with(['servicio', 'horas'])
            ->findOrFail($id);

        if ($actividad->servicio->id_alumno !== Auth::id()) {
            abort(403);
        }

        return view('alumno.actividades.show', compact('actividad'));
    }

    public function start($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);

        if ($actividad->servicio->id_alumno !== Auth::id()) {
            abort(403);
        }

        if ($actividad->servicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['actividad' => 'El conteo de horas aplica sólo para alumnos adelantando.']);
        }

        $abierta = Hora::where('id_actividad', $actividad->id_actividad)
            ->whereNull('hora_final')
            ->first();

        if ($abierta) {
            return back()->withErrors(['actividad' => 'Ya hay una sesión de trabajo abierta.']);
        }

        Hora::create([
            'hora_inicio' => now(),
            'hora_final' => null,
            'asistencia' => 'Aprobada',
            'id_actividad' => $actividad->id_actividad,
        ]);

        return back();
    }

    public function stop($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);

        if ($actividad->servicio->id_alumno !== Auth::id()) {
            abort(403);
        }

        if ($actividad->servicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['actividad' => 'El conteo de horas aplica sólo para alumnos adelantando.']);
        }

        $abierta = Hora::where('id_actividad', $actividad->id_actividad)
            ->whereNull('hora_final')
            ->first();

        if (!$abierta) {
            return back()->withErrors(['actividad' => 'No hay una sesión abierta para cerrar.']);
        }

        $abierta->update(['hora_final' => now()]);

        return back();
    }

    public function complete($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);

        if ($actividad->servicio->id_alumno !== Auth::id()) {
            abort(403);
        }

        $abierta = Hora::where('id_actividad', $actividad->id_actividad)
            ->whereNull('hora_final')
            ->first();

        if ($abierta) {
            $abierta->update(['hora_final' => now()]);
        }

        $actividad->update(['estado' => 'Activa']);

        return back();
    }

    public function informe()
    {
        $actividades = Actividad::whereHas('servicio', function ($q) {
                $q->where('id_alumno', Auth::id());
            })
            ->where('estado', 'Aprobada')
            ->orderBy('id_actividad', 'desc')
            ->get(['id_actividad', 'actividad']);

        return view('alumno.actividades.informe', compact('actividades'));
    }
}
