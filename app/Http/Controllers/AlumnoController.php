<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    // Listar todos los alumnos
    public function index()
    {
        $alumnos = Usuario::where('rol', 'alumno')->get();
        return view('profesor.alumnos.index', compact('alumnos'));
    }

    // Mostrar formulario de alta
    public function create()
    {
        return view("profesor.alumnos.crear");
    }

    // Guardar nuevo alumno
    public function store(Request $request)
    {
        // Validar datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos_p' => 'required|string|max:255',
            'apellidos_m' => 'nullable|string|max:255',
            'matricula' => 'required|unique:usuarios,matricula',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6'
        ]);

        // Crear el registro en la base de datos
        Usuario::create([
            "nombre" => $request->nombre,
            "apellidos_p" => $request->apellidos_p,
            "apellidos_m" => $request->apellidos_m,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "rol" => "alumno",
            "matricula" => $request->matricula
        ]);
        return redirect()->route("alumnos.index");
    }

    // Mostrar formulario de edicion
    public function edit($id)
    {
        $alumno = Usuario::findOrFail($id);
        return view("profesor.alumnos.editar", compact("alumno"));
    }

    // Actualizar datos del alumno
    public function update(Request $request, $id)
    {
        $alumno = Usuario::findOrFail($id);

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
            "nombre" => $request->nombre,
            "apellidos_p" => $request->apellidos_p,
            "apellidos_m" => $request->apellidos_m,
            "email" => $request->email,
            "rol" => "alumno",
            "matricula" => $request->matricula
        ]);

        // Actualizar contraseña solo si se envio una nueva
        if ($request->filled('password')) {
            $alumno->update([
                "password" => Hash::make($request->password)
            ]);
        }

        return redirect()->route("alumnos.index");
    }

    // Eliminar alumno
    public function destroy($id)
    {
        Usuario::destroy($id);
        return redirect()->route("alumnos.index");
    }
}