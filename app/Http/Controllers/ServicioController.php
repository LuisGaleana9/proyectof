<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Models\Dependencia;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    // Listar servicios del asesor actual
    public function index()
    {
        // Mostrar servicios donde el usuario autenticado es el asesor
        $servicios = Servicio::with(['alumno', 'dependencia'])
            ->where('id_profesor_asesor', Auth::id())
            ->get();

        return view('profesor.servicios.index', compact('servicios'));
    }

    // Mostrar formulario de nuevo servicio
    public function create()
    {
        // Obtener solo alumnos del profesor actual
        $alumnos = Usuario::where('rol', 'Alumno')
            ->where('profesor_id', Auth::id())
            ->get();
        $dependencias = Dependencia::all();

        return view('profesor.servicios.crear', compact('alumnos', 'dependencias'));
    }

    // Guardar nuevo servicio
    public function store(Request $request)
    {
        // Validar datos de entrada
        $request->validate([
            'id_alumno' => 'required|exists:usuarios,id_usuario',
            'id_dependencia' => 'required|exists:dependencias,id_dependencia',
            'tipo_servicio' => 'required|in:Regular,Adelantando',
            'fecha_inicio' => 'required|date',
        ]);

        // Verificar si el alumno ya tiene un servicio activo
        $existeServicio = Servicio::where('id_alumno', $request->id_alumno)
            ->where('estado_servicio', 'Activo')
            ->exists();

        if ($existeServicio) {
            return back()->withErrors(['id_alumno' => 'Este alumno ya tiene un servicio activo.']);
        }

        // Validar que el alumno pertenezca al profesor autenticado
        $alumnoValido = Usuario::where('id_usuario', $request->id_alumno)
            ->where('rol', 'Alumno')
            ->where('profesor_id', Auth::id())
            ->exists();

        if (!$alumnoValido) {
            return back()->withErrors(['id_alumno' => 'Solo puedes asignar servicios a alumnos creados por ti.']);
        }

        // Crear el registro del servicio
        Servicio::create([
            'id_alumno' => $request->id_alumno,
            'id_profesor_asesor' => Auth::id(), // El profesor logueado es el asesor
            'id_dependencia' => $request->id_dependencia,
            'tipo_servicio' => $request->tipo_servicio,
            'fecha_inicio' => $request->fecha_inicio,
            'estado_servicio' => 'Activo', // inicialmente estara activo
        ]);

        return redirect()->route('servicios.index');
    }

    // Mostrar formulario de edicion
    public function edit($id)
    {
        $servicio = Servicio::findOrFail($id);

        // Verificar que el servicio pertenezca al profesor logueado
        if ($servicio->id_profesor_asesor != Auth::id()) {
            abort(403);
        }

        $alumnos = Usuario::where('rol', 'Alumno')
            ->where('profesor_id', Auth::id())
            ->get();
        $dependencias = Dependencia::all();

        return view('profesor.servicios.editar', compact('servicio', 'alumnos', 'dependencias'));
    }

    // Actualizar servicio
    public function update(Request $request, $id)
    {
        $servicio = Servicio::findOrFail($id);

        // Verificar que el servicio pertenezca al profesor logueado
        if ($servicio->id_profesor_asesor != Auth::id()) {
            abort(403);
        }

        // Validar datos actualizados
        $request->validate([
            'id_alumno' => 'required|exists:usuarios,id_usuario',
            'id_dependencia' => 'required|exists:dependencias,id_dependencia',
            'tipo_servicio' => 'required|in:Regular,Adelantando',
            'fecha_inicio' => 'required|date',
            'estado_servicio' => 'required|in:Activo,En pausa,Finalizado',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
        ]);

        $servicio->update($request->all());

        return redirect()->route('servicios.index');
    }

    // Eliminar servicio
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);

        // Verificar que el servicio pertenezca al profesor logueado
        if ($servicio->id_profesor_asesor != Auth::id()) {
            abort(403);
        }

        $servicio->delete();

        return redirect()->route('servicios.index');
    }
}