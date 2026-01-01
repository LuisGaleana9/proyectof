<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

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
        Usuario::create([
            "nombre" => $request->nombre,
            "apellidos_p" => $request->apellidos_p,
            "apellidos_m" => $request->apellidos_m,
            "email" => $request->email,
            "password" => $request->password,
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
        $profesor->update([
            "nombre" => $request->nombre,
            "apellidos_p" => $request->apellidos_p,
            "apellidos_m" => $request->apellidos_m,
            "email" => $request->email,
            "password" => $request->password,
            "rol" => "profesor",
            "matricula" => $request->matricula
        ]);
        return redirect()->route("profesores.index");
    }

    public function destroy($id)
    {
        Usuario::destroy($id);
        return redirect()->route("profesores.index");
    }
}