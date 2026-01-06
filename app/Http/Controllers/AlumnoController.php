<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    public function index()
    {
        $alumnos = Usuario::where('rol', 'alumno')->get();
        return view('profesor.alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        return view("profesor.alumnos.crear");
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos_p' => 'required|string|max:255',
            'apellidos_m' => 'nullable|string|max:255',
            'matricula' => 'required|unique:usuarios,matricula',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6'
        ]);

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

    public function edit($id)
    {
        $alumno = Usuario::findOrFail($id);
        return view("profesor.alumnos.editar", compact("alumno"));
    }

    public function update(Request $request, $id)
    {
        $alumno = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos_p' => 'required|string|max:255',
            'apellidos_m' => 'nullable|string|max:255',
            'matricula' => 'required|unique:usuarios,matricula,' . $alumno->id_usuario . ',id_usuario',
            'email' => 'required|email|unique:usuarios,email,' . $alumno->id_usuario . ',id_usuario',
        ]);

        $alumno->update([
            "nombre" => $request->nombre,
            "apellidos_p" => $request->apellidos_p,
            "apellidos_m" => $request->apellidos_m,
            "email" => $request->email,
            "rol" => "alumno",
            "matricula" => $request->matricula
        ]);

        if ($request->filled('password')) {
            $alumno->update([
                "password" => Hash::make($request->password)
            ]);
        }

        return redirect()->route("alumnos.index");
    }

    public function destroy($id)
    {
        Usuario::destroy($id);
        return redirect()->route("alumnos.index");
    }
}
