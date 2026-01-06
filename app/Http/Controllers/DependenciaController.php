<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dependencia;
use App\Models\Usuario;

class DependenciaController extends Controller
{
    public function index()
    {
        // Obtenemos todas las dependencias con su responsable
        $dependencias = Dependencia::with('responsable')->get();
        return view('admin.dependencias.index', compact('dependencias'));
    }

    public function create()
    {
        $profesores = Usuario::where('rol', 'profesor')->get();
        return view('admin.dependencias.crear', compact('profesores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:dependencias,nombre',
            'id_profesor_responsable' => 'required|exists:usuarios,id_usuario'
        ]);

        Dependencia::create($request->all());

        return redirect()->route('dependencias.index');
    }

    public function edit($id)
    {
        $dependencia = Dependencia::findOrFail($id);
        $profesores = Usuario::where('rol', 'profesor')->get();
        return view('admin.dependencias.editar', compact('dependencia', 'profesores'));
    }

    public function update(Request $request, $id)
    {
        $dependencia = Dependencia::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:dependencias,nombre,' . $id . ',id_dependencia',
            'id_profesor_responsable' => 'required|exists:usuarios,id_usuario'
        ]);

        $dependencia->update($request->all());

        return redirect()->route('dependencias.index');
    }

    public function destroy($id)
    {
        $dependencia = Dependencia::findOrFail($id);
        $dependencia->delete();

        return redirect()->route('dependencias.index');
    }

}