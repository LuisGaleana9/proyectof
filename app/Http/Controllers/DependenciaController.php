<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dependencia;

class DependenciaController extends Controller
{
    // Listar dependencias
    public function index()
    {
        $dependencias = Dependencia::all();
        return view('admin.dependencias.index', compact('dependencias'));
    }

    // Mostrar formulario de alta
    public function create()
    {
        return view('admin.dependencias.crear');
    }

    // Guardar nueva dependencia
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:dependencias,nombre',
        ]);

        Dependencia::create(['nombre' => $request->nombre]);

        return redirect()->route('dependencias.index');
    }

    // Mostrar formulario de edicion
    public function edit($id)
    {
        $dependencia = Dependencia::findOrFail($id);
        return view('admin.dependencias.editar', compact('dependencia'));
    }

    // Actualizar dependencia
    public function update(Request $request, $id)
    {
        $dependencia = Dependencia::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:dependencias,nombre,' . $id . ',id_dependencia',
        ]);

        $dependencia->update(['nombre' => $request->nombre]);

        return redirect()->route('dependencias.index');
    }

    // Eliminar dependencia
    public function destroy($id)
    {
        $dependencia = Dependencia::findOrFail($id);
        $dependencia->delete();

        return redirect()->route('dependencias.index');
    }
}