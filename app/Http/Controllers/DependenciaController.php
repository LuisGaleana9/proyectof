<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dependencia;
use App\Models\Usuario;

class DependenciaController extends Controller
{
    // Listar dependencias
    public function index()
    {
        // Obtenemos todas las dependencias con su responsable
        $dependencias = Dependencia::with('responsable')->get();
        return view('admin.dependencias.index', compact('dependencias'));
    }

    // Mostrar formulario de alta
    public function create()
    {
        // Obtener profesores para asignar como responsables
        $profesores = Usuario::where('rol', 'profesor')->get();
        return view('admin.dependencias.crear', compact('profesores'));
    }

    // Guardar nueva dependencia
    public function store(Request $request)
    {
        // Validar datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255|unique:dependencias,nombre',
            'id_profesor_responsable' => 'required|exists:usuarios,id_usuario'
        ]);

        // Crear el registro en la base de datos
        Dependencia::create($request->all());

        return redirect()->route('dependencias.index');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $dependencia = Dependencia::findOrFail($id);
        $profesores = Usuario::where('rol', 'profesor')->get();
        return view('admin.dependencias.editar', compact('dependencia', 'profesores'));
    }

    // Actualizar dependencia
    public function update(Request $request, $id)
    {
        $dependencia = Dependencia::findOrFail($id);

        // Validar datos modificados
        $request->validate([
            'nombre' => 'required|string|max:255|unique:dependencias,nombre,' . $id . ',id_dependencia',
            'id_profesor_responsable' => 'required|exists:usuarios,id_usuario'
        ]);

        $dependencia->update($request->all());

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