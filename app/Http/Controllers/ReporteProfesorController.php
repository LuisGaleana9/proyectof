<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteProfesorController extends Controller
{

    //Listar reportes enviador por alumno
    public function index()
    {
        $profesorId = Auth::user()->id_usuario;

        $reportes = Reporte::with(['alumnoServicio.alumno', 'alumnoServicio.servicio'])
            ->where('estado', 'Enviado')
            ->whereHas('alumnoServicio.servicio', function ($q) use ($profesorId) {
                $q->where('id_profesor', $profesorId);
            })
            ->orderBy('fecha_entrega', 'asc')
            ->get();

        return view('profesor.reportes.index', ['reportes' => $reportes]);
    }

    public function revisar($id)
    {
        $profesorId = Auth::user()->id_usuario;

        $reporte = Reporte::with([
            'alumnoServicio.alumno',
            'alumnoServicio.servicio.profesor.dependencia',
        ])->findOrFail($id);

        // Verificar que pertenece a un servicio del profesor
        if ($reporte->alumnoServicio->servicio->id_profesor !== $profesorId) {
            abort(403);
        }

        return view('profesor.reportes.revisar', [
            'reporte' => $reporte,
            'alumno' => $reporte->alumnoServicio->alumno,
            'servicio' => $reporte->alumnoServicio->servicio,
            'inscripcion' => $reporte->alumnoServicio,
        ]);
    }

    // Aprobar reporte
    public function aprobar($id)
    {
        $profesorId = Auth::user()->id_usuario;

        $reporte = Reporte::with('alumnoServicio.servicio')->findOrFail($id);

        if ($reporte->alumnoServicio->servicio->id_profesor !== $profesorId) {
            abort(403);
        }

        if ($reporte->estado !== 'Enviado') {
            return back()->withErrors(['Solo puedes aprobar reportes en estado Enviado.']);
        }

        $reporte->update([
            'estado' => 'Aprobado',
            'fecha_revision' => now(),
        ]);

        return redirect()->route('profesor.reportes.index')
            ->with('status', 'Reporte aprobado correctamente.');
    }

    // Rechazar un reporte
    public function rechazar($id)
    {
        $profesorId = Auth::user()->id_usuario;

        $reporte = Reporte::with('alumnoServicio.servicio')->findOrFail($id);

        if ($reporte->alumnoServicio->servicio->id_profesor !== $profesorId) {
            abort(403);
        }

        if ($reporte->estado !== 'Enviado') {
            return back()->withErrors(['Solo puedes rechazar reportes en estado Enviado.']);
        }

        $reporte->update([
            'estado' => 'Rechazado',
            'fecha_revision' => now(),
        ]);

        return redirect()->route('profesor.reportes.index')
            ->with('status', 'Reporte rechazado.');
    }

    // Correciones a un reporte
    public function corregir(Request $request, $id)
    {
        $profesorId = Auth::user()->id_usuario;

        $reporte = Reporte::with('alumnoServicio.servicio')->findOrFail($id);

        if ($reporte->alumnoServicio->servicio->id_profesor !== $profesorId) {
            abort(403);
        }

        if ($reporte->estado !== 'Enviado') {
            return back()->withErrors(['Solo puedes solicitar correcciones de reportes en estado Enviado.']);
        }

        $request->validate([
            'correcciones' => 'required|string|min:10',
        ], [
            'correcciones.required' => 'Debes escribir las correcciones que esperas del alumno.',
            'correcciones.min' => 'Las correcciones deben tener al menos 10 caracteres.',
        ]);

        $reporte->update([
            'estado' => 'Corregir',
            'correcciones' => $request->correcciones,
            'fecha_revision' => now(),
        ]);

        return redirect()->route('profesor.reportes.index')
            ->with('status', 'Se han enviado las correcciones al alumno.');
    }
}
