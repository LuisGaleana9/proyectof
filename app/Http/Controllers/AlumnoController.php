<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Servicio;
use App\Models\AlumnoServicio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    // Listar todos los alumnos del profesor
    public function index()
    {
        $alumnos = Usuario::where('rol', 'Alumno')
            ->where('profesor_id', Auth::id())
            ->with('alumnoServicios.servicio')
            ->get();
        return view('profesor.alumnos.index', compact('alumnos'));
    }

    // Mostrar formulario de alta
    public function create()
    {
        // Cargar servicios del profesor para el dropdown
        $servicios = Servicio::where('id_profesor', Auth::id())->get();
        return view('profesor.alumnos.crear', compact('servicios'));
    }

    // Guardar nuevo alumno
    public function store(Request $request)
    {
        $matricula = trim((string) $request->matricula);
        $emailGenerado = strtolower($matricula) . '@umich.mx';

        $request->merge([
            'matricula' => $matricula,
            'email' => $emailGenerado,
        ]);

        // Validar datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos_p' => 'required|string|max:255',
            'apellidos_m' => 'nullable|string|max:255',
            'matricula' => 'required|unique:usuarios,matricula',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'tipo_servicio' => 'required|in:Regular,Adelantando',
            'fecha_inicio' => 'required|date',
        ]);

        // Verificar que el servicio pertenezca al profesor
        $servicio = Servicio::where('id_servicio', $request->id_servicio)
            ->where('id_profesor', Auth::id())
            ->firstOrFail();

        // Crear el alumno
        $alumno = Usuario::create([
            'nombre' => $request->nombre,
            'apellidos_p' => $request->apellidos_p,
            'apellidos_m' => $request->apellidos_m,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'Alumno',
            'matricula' => $request->matricula,
            'profesor_id' => Auth::id(),
        ]);

        // Inscribir al alumno en el servicio
        AlumnoServicio::create([
            'id_alumno' => $alumno->id_usuario,
            'id_servicio' => $servicio->id_servicio,
            'tipo_servicio' => $request->tipo_servicio,
            'fecha_inicio' => $request->fecha_inicio,
            'estado_servicio' => 'Activo',
        ]);

        return redirect()->route('alumnos.index');
    }

    // Mostrar formulario de edicion
    public function edit($id)
    {
        $alumno = Usuario::where('id_usuario', $id)
            ->where('profesor_id', Auth::id())
            ->where('rol', 'Alumno')
            ->firstOrFail();

        $servicios = Servicio::where('id_profesor', Auth::id())->get();

        // Inscripcion actual del alumno
        $inscripcion = AlumnoServicio::where('id_alumno', $alumno->id_usuario)
            ->where('estado_servicio', 'Activo')
            ->first();

        return view('profesor.alumnos.editar', compact('alumno', 'servicios', 'inscripcion'));
    }

    // Actualizar datos del alumno
    public function update(Request $request, $id)
    {
        $alumno = Usuario::where('id_usuario', $id)
            ->where('profesor_id', Auth::id())
            ->where('rol', 'Alumno')
            ->firstOrFail();

        // Validar los datos modificados
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos_p' => 'required|string|max:255',
            'apellidos_m' => 'nullable|string|max:255',
            'matricula' => 'required|unique:usuarios,matricula,' . $alumno->id_usuario . ',id_usuario',
            'email' => 'required|email|unique:usuarios,email,' . $alumno->id_usuario . ',id_usuario',
        ]);

        // Actualizar informacion basica
        $alumno->update([
            'nombre' => $request->nombre,
            'apellidos_p' => $request->apellidos_p,
            'apellidos_m' => $request->apellidos_m,
            'email' => $request->email,
            'rol' => 'Alumno',
            'matricula' => $request->matricula,
        ]);

        // Actualizar contrasena solo si se envio una nueva
        if ($request->filled('password')) {
            $alumno->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect()->route('alumnos.index');
    }

    // Eliminar alumno
    public function destroy($id)
    {
        $alumno = Usuario::where('id_usuario', $id)
            ->where('profesor_id', Auth::id())
            ->where('rol', 'Alumno')
            ->firstOrFail();
        $alumno->delete();
        return redirect()->route('alumnos.index');
    }
}