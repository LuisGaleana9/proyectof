<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Actividad;

class ProfesorActividadController extends Controller
{
    public function pendientes()
    {
        $actividades = Actividad::with('servicio')
            ->where('estado', 'Activa')
            ->whereHas('servicio', function ($q) {
                $q->where('id_profesor_asesor', Auth::id());
            })
            ->get();

        return view('profesor.actividades.pendientes', compact('actividades'));
    }

    public function confirmar($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);

        if ($actividad->servicio->id_profesor_asesor !== Auth::id()) {
            abort(403);
        }

        $actividad->update(['estado' => 'Aprobada']);

        return back();
    }

    public function denegar($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);

        if ($actividad->servicio->id_profesor_asesor !== Auth::id()) {
            abort(403);
        }

        $actividad->update(['estado' => 'Rechazada']);

        return back();
    }
}
