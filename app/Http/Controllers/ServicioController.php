<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    // Listar servicios del profesor actual
    public function index()
    {
        $servicios = Servicio::withCount('alumnoServicios')
            ->where('id_profesor', Auth::id())
            ->get();

        return view('profesor.servicios.index', compact('servicios'));
    }

    // Mostrar formulario de nuevo servicio
    public function create()
    {
        return view('profesor.servicios.crear');
    }

    // Guardar nuevo servicio
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        Servicio::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_profesor' => Auth::id(),
        ]);

        return redirect()->route('servicios.index');
    }

    // Mostrar formulario de edicion
    public function edit($id)
    {
        $servicio = Servicio::where('id_servicio', $id)
            ->where('id_profesor', Auth::id())
            ->firstOrFail();

        $alumnosInscritos = $servicio->alumnoServicios()->with('alumno')->get();

        return view('profesor.servicios.editar', compact('servicio', 'alumnosInscritos'));
    }

    // Actualizar servicio
    public function update(Request $request, $id)
    {
        $servicio = Servicio::where('id_servicio', $id)
            ->where('id_profesor', Auth::id())
            ->firstOrFail();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        $servicio->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('servicios.index');
    }

    // Eliminar servicio
    public function destroy($id)
    {
        $servicio = Servicio::where('id_servicio', $id)
            ->where('id_profesor', Auth::id())
            ->firstOrFail();

        $servicio->delete();

        return redirect()->route('servicios.index');
    }
}