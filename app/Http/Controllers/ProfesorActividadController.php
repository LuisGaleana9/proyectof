<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Servicio;
use App\Models\AlumnoServicio;
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
        $actividades = Actividad::with(['servicio', 'alumnoServicio.alumno', 'horas'])
            ->whereHas('servicio', function ($q) use ($profesorId) {
                $q->where('id_profesor', $profesorId);
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
        $servicios = Servicio::where('id_profesor', $profesorId)->get();

        // Obtener inscripciones activas para cada servicio
        $alumnosPorServicio = AlumnoServicio::whereIn('id_servicio', $servicios->pluck('id_servicio'))
            ->where('estado_servicio', 'Activo')
            ->with('alumno')
            ->get()
            ->groupBy('id_servicio');

        return view('profesor.actividades.crear', compact('servicios', 'alumnosPorServicio'));
    }

    // Guardar nueva actividad
    public function store(Request $request)
    {
        $request->validate([
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'actividad' => 'required|string|max:500',
            'comentarios' => 'nullable|string|max:1000',
            'fecha_limite' => 'required|date',
            'id_alumno_servicio' => 'required|exists:alumno_servicio,id',
        ]);

        $servicio = Servicio::where('id_servicio', $request->id_servicio)
            ->where('id_profesor', Auth::id())
            ->firstOrFail();

        $data = [
            'id_servicio' => $servicio->id_servicio,
            'actividad' => $request->actividad,
            'comentarios' => $request->comentarios,
            'fecha_limite' => $request->fecha_limite,
            'estado' => 'Activa',
            'id_alumno_servicio' => $request->id_alumno_servicio,
        ];

        Actividad::create($data);

        return redirect()->route('profesor.actividades.index')->with('status', 'Actividad creada.');
    }

    // Mostrar formulario de edicion
    public function edit($id)
    {
        $actividad = Actividad::with(['servicio', 'alumnoServicio.alumno'])->findOrFail($id);
        if ($actividad->servicio->id_profesor != Auth::id()) {
            abort(403);
        }

        $alumnosServicio = AlumnoServicio::where('id_servicio', $actividad->id_servicio)
            ->where('estado_servicio', 'Activo')
            ->with('alumno')
            ->get();

        return view('profesor.actividades.editar', compact('actividad', 'alumnosServicio'));
    }

    // Actualizar actividad
    public function update(Request $request, $id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        if ($actividad->servicio->id_profesor != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'actividad' => 'required|string|max:500',
            'comentarios' => 'nullable|string|max:1000',
            'fecha_limite' => 'required|date',
            'id_alumno_servicio' => 'required|exists:alumno_servicio,id',
        ]);

        $actividad->update([
            'actividad' => $request->actividad,
            'comentarios' => $request->comentarios,
            'fecha_limite' => $request->fecha_limite,
            'id_alumno_servicio' => $request->id_alumno_servicio,
        ]);

        return redirect()->route('profesor.actividades.index')->with('status', 'Actividad actualizada.');
    }

    // Eliminar actividad
    public function destroy($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        if ($actividad->servicio->id_profesor != Auth::id()) {
            abort(403);
        }

        $actividad->delete();

        return redirect()->route('profesor.actividades.index')->with('status', 'Actividad eliminada.');
    }
}
