<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\AlumnoServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReporteAlumnoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Obtener inscripciones regular del alumno con sus reportes
        $inscripciones = AlumnoServicio::where('id_alumno', $user->id_usuario)
            ->where('tipo_servicio', 'Regular')
            ->with(['reportes' => function ($q) {
                $q->orderBy('numero_reporte', 'asc');
            }, 'servicio'])
            ->get();

        // Buscar el proximo reporte pendiente
        $proximoReporte = null;
        foreach ($inscripciones as $inscripcion) {
            foreach ($inscripcion->reportes as $reporte) {
                if (in_array($reporte->estado, ['Pendiente', 'Corregir'])) {
                    $proximoReporte = $reporte;
                    break 2;
                }
            }
        }

        return view('alumno.reportes.index', [
            'inscripciones' => $inscripciones,
            'proximoReporte' => $proximoReporte,
            'user' => $user,
        ]);
    }

    public function escribir($id)
    {
        $user = Auth::user();

        $reporte = Reporte::with(['alumnoServicio.alumno', 'alumnoServicio.servicio'])
            ->findOrFail($id);

        // Verificar que el reporte pertenece al alumno
        if ($reporte->alumnoServicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        // Verificar que puede escribir
        if (!$reporte->puedeEscribir()) {
            return redirect()->route('alumno.reportes.index')
                ->withErrors(['No puedes escribir este reporte en este momento.']);
        }

        return view('alumno.reportes.escribir', [
            'reporte' => $reporte,
            'user' => $user,
        ]);
    }

    public function enviar(Request $request, $id)
    {
        $user = Auth::user();

        $reporte = Reporte::with(['alumnoServicio.alumno', 'alumnoServicio.servicio'])
            ->findOrFail($id);

        // Verificar pertenencia
        if ($reporte->alumnoServicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        // Verificar que puede escribir
        if (!$reporte->puedeEscribir()) {
            return redirect()->route('alumno.reportes.index')
                ->withErrors(['No puedes enviar este reporte en este momento.']);
        }

        $request->validate([
            'contenido' => 'required|string|min:50',
        ], [
            'contenido.required' => 'Debes escribir el contenido del reporte.',
            'contenido.min' => 'El reporte debe tener al menos 50 caracteres.',
        ]);

        $reporte->update([
            'contenido' => $request->contenido,
            'estado' => 'Enviado',
            'fecha_envio' => now(),
            'correcciones' => null,
        ]);

        return redirect()->route('alumno.reportes.index')
            ->with('status', 'Tu ' . $reporte->nombreReporte() . ' ha sido enviado correctamente. Tu profesor lo revisará pronto.');
    }

    public function descargarPdf($id)
    {
        $user = Auth::user();

        $reporte = Reporte::with([
            'alumnoServicio.alumno',
            'alumnoServicio.servicio.profesor.dependencia',
        ])->findOrFail($id);

        // Verificar pertenencia
        if ($reporte->alumnoServicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        // Solo se puede descargar si esta aprobado
        if ($reporte->estado !== 'Aprobado') {
            return redirect()->route('alumno.reportes.index')
                ->withErrors(['Solo puedes descargar reportes aprobados.']);
        }

        $pdf = Pdf::loadView('pdf.reporte', [
            'reporte' => $reporte,
            'alumno' => $reporte->alumnoServicio->alumno,
            'servicio' => $reporte->alumnoServicio->servicio,
            'inscripcion' => $reporte->alumnoServicio,
        ]);

        $nombreArchivo = 'reporte_' . $reporte->tipo . '_' . $reporte->numero_reporte . '_' . $user->matricula . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
