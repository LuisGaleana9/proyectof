<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class ProfesorController extends Controller
{
    public function index()
    {
        $profesores = Usuario::where('rol', 'profesor')->get();
        return view('admin.profesores.index', compact('profesores'));
    }

    public function create()
    {
        return view("admin.profesores.crear");
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
            "rol" => "profesor",
            "matricula" => $request->matricula
        ]);
        return redirect()->route("profesores.index");
    }

    public function edit($id)
    {
        $profesor = Usuario::findOrFail($id);
        return view("admin.profesores.editar", compact("profesor"));
    }

    public function update(Request $request, $id)
    {
        $profesor = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos_p' => 'required|string|max:255',
            'apellidos_m' => 'nullable|string|max:255',
            'matricula' => 'required|unique:usuarios,matricula,' . $profesor->id_usuario . ',id_usuario',
            'email' => 'required|email|unique:usuarios,email,' . $profesor->id_usuario . ',id_usuario',
        ]);

        $profesor->update([
            "nombre" => $request->nombre,
            "apellidos_p" => $request->apellidos_p,
            "apellidos_m" => $request->apellidos_m,
            "email" => $request->email,
            "rol" => "profesor",
            "matricula" => $request->matricula
        ]);

        if ($request->filled('password')) {
            $profesor->update([
                "password" => Hash::make($request->password)
            ]);
        }

        return redirect()->route("profesores.index");
    }

    public function destroy($id)
    {
        Usuario::destroy($id);
        return redirect()->route("profesores.index");
    }
}