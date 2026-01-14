<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class ProfesorController extends Controller
{
    // Listar todos los profesores
    public function index()
    {
        $profesores = Usuario::where('rol', 'profesor')->get();
        return view('admin.profesores.index', compact('profesores'));
    }

    // Mostrar el formulario de alta
    public function create()
    {
        return view("admin.profesores.crear");
    }

    // Guardar nuevo profesor
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
            "rol" => "profesor",
            "matricula" => $request->matricula
        ]);

        return redirect()->route("profesores.index");
    }

    // Mostrar formulario de edicion
    public function edit($id)
    {
        $profesor = Usuario::findOrFail($id);
        return view("admin.profesores.editar", compact("profesor"));
    }

    // Actualizar datos del profesor
    public function update(Request $request, $id)
    {
        $profesor = Usuario::findOrFail($id);

        // Validar los datos modificados 
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos_p' => 'required|string|max:255',
            'apellidos_m' => 'nullable|string|max:255',
            'matricula' => 'required|unique:usuarios,matricula,' . $profesor->id_usuario . ',id_usuario',
            'email' => 'required|email|unique:usuarios,email,' . $profesor->id_usuario . ',id_usuario',
        ]);

        // Actualizar informacion basica
        $profesor->update([
            "nombre" => $request->nombre,
            "apellidos_p" => $request->apellidos_p,
            "apellidos_m" => $request->apellidos_m,
            "email" => $request->email,
            "rol" => "profesor",
            "matricula" => $request->matricula
        ]);

        // Actualizar contraseña solo si se envio una nueva
        if ($request->filled('password')) {
            $profesor->update([
                "password" => Hash::make($request->password)
            ]);
        }

        return redirect()->route("profesores.index");
    }

    // Eliminar profesor
    public function destroy($id)
    {
        Usuario::destroy($id);
        return redirect()->route("profesores.index");
    }
}