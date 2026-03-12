<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Actividad;
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

        // Calcular periodo del bimestre
        $inscripcion = $reporte->alumnoServicio;
        $fechaInicio = Carbon::parse($inscripcion->fecha_inicio);

        if ($reporte->tipo === 'General') {
            $periodoInicio = $fechaInicio->copy();
            $periodoFin = $fechaInicio->copy()->addMonths(6);
        } else {
            $periodoInicio = $fechaInicio->copy()->addMonths(($reporte->numero_reporte - 1) * 2);
            $periodoFin = $fechaInicio->copy()->addMonths($reporte->numero_reporte * 2);
        }

        // Obtener actividades aprobadas del periodo (individuales del alumno + grupales del servicio)
        $actividades = Actividad::where('estado', 'Aprobada')
            ->where(function ($q) use ($inscripcion) {
                $q->where('id_alumno_servicio', $inscripcion->id)
                  ->orWhere(function ($q2) use ($inscripcion) {
                      $q2->where('id_servicio', $inscripcion->id_servicio)
                         ->whereNull('id_alumno_servicio');
                  });
            })
            ->whereBetween('fecha_limite', [$periodoInicio, $periodoFin])
            ->orderBy('fecha_limite', 'asc')
            ->get();

        // Generar mensaje pre-llenado solo si no tiene contenido previo
        $mensajePrellenado = null;
        if (empty($reporte->contenido)) {
            $inicioTexto = $periodoInicio->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y');
            $finTexto = $periodoFin->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y');

            if ($actividades->isNotEmpty()) {
                $listaActividades = $actividades->pluck('actividad')->implode(', ');
                $mensajePrellenado = "Durante el periodo del {$inicioTexto} al {$finTexto}, se realizaron las siguientes actividades: {$listaActividades}. "
                    . "Estas actividades contribuyeron al cumplimiento de los objetivos del programa de servicio social.";
            } else {
                $mensajePrellenado = "Durante el periodo del {$inicioTexto} al {$finTexto}, se llevaron a cabo diversas actividades en el marco del programa de servicio social.";
            }
        }

        return view('alumno.reportes.escribir', [
            'reporte' => $reporte,
            'user' => $user,
            'actividades' => $actividades,
            'periodoInicio' => $periodoInicio,
            'periodoFin' => $periodoFin,
            'mensajePrellenado' => $mensajePrellenado,
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
