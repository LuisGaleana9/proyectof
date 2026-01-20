<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ProfesorActividadController extends Controller
{
    // Calcular horas acumuladas por actividad
    private function calcularTotalHoras(Actividad $actividad): float
    {
        if (Schema::hasColumn('horas', 'horas_totales')) {
            return (float) $actividad->horas->sum('horas_totales');
        }

        return $actividad->horas->reduce(function ($carry, $h) {
            if ($h->hora_final) {
                $inicio = Carbon::parse($h->hora_inicio);
                $fin = Carbon::parse($h->hora_final);
                $carry += round($inicio->diffInMinutes($fin) / 60, 2);
            }
            return $carry;
        }, 0.0);
    }

    // Listar actividades del profesor
    public function index()
    {
        $profesorId = Auth::id();
        $actividades = Actividad::with(['servicio.alumno', 'horas'])
            ->whereHas('servicio', function ($q) use ($profesorId) {
                $q->where('id_profesor_asesor', $profesorId);
            })
            ->orderBy('fecha_limite', 'desc')
            ->get();

        $actividades->each(function ($a) {
            $a->total_horas_calculadas = $this->calcularTotalHoras($a);
        });

        return view('profesor.actividades.index', compact('actividades'));
    }

    // Formulario para crear actividad
    public function create()
    {
        $profesorId = Auth::id();
        $servicios = Servicio::with('alumno')
            ->where('id_profesor_asesor', $profesorId)
            ->where('estado_servicio', 'Activo')
            ->get();

        return view('profesor.actividades.crear', compact('servicios'));
    }

    // Guardar nueva actividad
    public function store(Request $request)
    {
        $request->validate([
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'actividad' => 'required|string|max:500',
            'comentarios' => 'nullable|string|max:1000',
            'fecha_limite' => 'required|date',
        ]);

        $servicio = Servicio::where('id_servicio', $request->id_servicio)
            ->where('id_profesor_asesor', Auth::id())
            ->firstOrFail();

        $data = [
            'id_servicio' => $servicio->id_servicio,
            'id_alumno' => $servicio->id_alumno,
            'actividad' => $request->actividad,
            'comentarios' => $request->comentarios,
            'fecha_limite' => $request->fecha_limite,
            'estado' => 'Activa',
        ];

        Actividad::create($data);

        return redirect()->route('profesor.actividades.index')->with('status', 'Actividad creada.');
    }

    // Mostrar formulario de edicion
    public function edit($id)
    {
        $actividad = Actividad::with('servicio.alumno')->findOrFail($id);
        if ($actividad->servicio->id_profesor_asesor != Auth::id()) {
            abort(403);
        }

        return view('profesor.actividades.editar', compact('actividad'));
    }

    // Actualizar actividad
    public function update(Request $request, $id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        if ($actividad->servicio->id_profesor_asesor != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'actividad' => 'required|string|max:500',
            'comentarios' => 'nullable|string|max:1000',
            'fecha_limite' => 'required|date',
        ]);

        $actividad->update([
            'actividad' => $request->actividad,
            'comentarios' => $request->comentarios,
            'fecha_limite' => $request->fecha_limite,
        ]);

        return redirect()->route('profesor.actividades.index')->with('status', 'Actividad actualizada.');
    }

    // Eliminar actividad
    public function destroy($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        if ($actividad->servicio->id_profesor_asesor != Auth::id()) {
            abort(403);
        }

        $actividad->delete();

        return redirect()->route('profesor.actividades.index')->with('status', 'Actividad eliminada.');
    }
}
