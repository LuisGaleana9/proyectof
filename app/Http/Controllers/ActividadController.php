<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ActividadController extends Controller
{
    // Calcular minutos acumulados por actividad
    private function calcularTotalMinutos(Actividad $actividad): int
    {
        if (Schema::hasColumn('horas', 'horas_totales')) {
            $horas = (float) $actividad->horas->sum('horas_totales');
            return (int) round($horas * 60);
        }

        return (int) $actividad->horas->reduce(function ($carry, $h) {
            if ($h->hora_final) {
                $inicio = Carbon::parse($h->hora_inicio);
                $fin = Carbon::parse($h->hora_final);
                $carry += $inicio->diffInMinutes($fin);
            }
            return $carry;
        }, 0);
    }

    // Mostrar detalle de actividad para alumno
    public function show($id)
    {
        $actividad = Actividad::with(['servicio', 'horas'])->findOrFail($id);

        $user = Auth::user();
        // Validar que la actividad pertenezca al alumno
        if ($actividad->servicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        $tipoServicio = $actividad->servicio->tipo_servicio;
        $horaAbierta = $actividad->horas()->whereNull('hora_final')->latest('hora_inicio')->first();
        $totalMinutos = null;
        if ($tipoServicio === 'Adelantando') {
            $totalMinutos = $this->calcularTotalMinutos($actividad);
        }

        return view('alumno.actividad', [
            'actividad' => $actividad,
            'tipoServicio' => $tipoServicio,
            'horaAbierta' => $horaAbierta,
            'totalMinutos' => $totalMinutos,
        ]);
    }

    // Registrar entrada de horas (Adelantando)
    public function checkIn($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $user = Auth::user();
        if ($actividad->servicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        if ($actividad->servicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['No disponible para tipo Regular.']);
        }

        // Solo se permite si esta activa
        if ($actividad->estado !== 'Activa') {
            return back()->withErrors(['La actividad no está activa para registrar horas.']);
        }

        $horaAbierta = $actividad->horas()->whereNull('hora_final')->first();
        if ($horaAbierta) {
            return back()->withErrors(['Ya existe un registro de horas abierto.']);
        }

        $actividad->horas()->create([
            'hora_inicio' => now(),
            'asistencia' => 'Aprobada',
        ]);

        return back()->with('status', 'Check-in registrado.');
    }

    // Registrar salida y cerrar horas (Adelantando)
    public function checkOut($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $user = Auth::user();
        if ($actividad->servicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        if ($actividad->servicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['No disponible para tipo Regular.']);
        }

        $horaAbierta = $actividad->horas()->whereNull('hora_final')->latest('hora_inicio')->first();
        if (!$horaAbierta) {
            return back()->withErrors(['No hay registro de horas abierto.']);
        }

        $horaAbierta->hora_final = now();

        $inicio = Carbon::parse($horaAbierta->hora_inicio);
        $fin = Carbon::parse($horaAbierta->hora_final);
        $diffMinutes = $inicio->diffInMinutes($fin);
        $horasDecimales = round($diffMinutes / 60, 2);

        if (Schema::hasColumn('horas', 'horas_totales')) {
            $horaAbierta->horas_totales = $horasDecimales;
        }

        $horaAbierta->save();

        return back()->with('status', 'Check-out registrado.');
    }

    // Marcar actividad como realizada (Regular)
    public function marcarRealizada($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $user = Auth::user();
        if ($actividad->servicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        if ($actividad->servicio->tipo_servicio !== 'Regular') {
            return back()->withErrors(['Solo disponible para tipo Regular.']);
        }

        if ($actividad->estado !== 'Activa') {
            return back()->withErrors(['La actividad ya no está activa para enviarse a revisión.']);
        }

        $actividad->estado = 'En Revisión';
        $actividad->save();

        return back()->with('status', 'Actividad enviada a revisión.');
    }

    // Enviar actividad a revision (Adelantando)
    public function enviarRevision($id)
    {
        $actividad = Actividad::with(['servicio', 'horas'])->findOrFail($id);
        $user = Auth::user();
        if ($actividad->servicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        if ($actividad->servicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['Solo disponible para tipo Adelantando.']);
        }

        if ($actividad->estado !== 'Activa') {
            return back()->withErrors(['La actividad ya no está activa para enviarse a revisión.']);
        }

        $horaAbierta = $actividad->horas()->whereNull('hora_final')->first();
        if ($horaAbierta) {
            return back()->withErrors(['No puedes enviar a revisión con una hora abierta.']);
        }

        $actividad->estado = 'En Revisión';
        $actividad->save();

        $totalMinutos = $this->calcularTotalMinutos($actividad);
        $horas = intdiv($totalMinutos, 60);
        $minutos = $totalMinutos % 60;

        return back()->with('status', 'Actividad enviada a revisión. Total: ' . $horas . 'h ' . $minutos . 'm');
    }

    // Cancelar revision y volver a activa
    public function cancelarRevision($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $user = Auth::user();
        if ($actividad->servicio->id_alumno !== $user->id_usuario) {
            abort(403);
        }

        return back()->withErrors(['Una actividad en revisión ya no puede ser cancelada por el alumno.']);
    }

    // Reporte del alumno con actividades aprobadas
    public function reporte(Request $request)
    {
        $user = Auth::user();
        // Servicios del alumno
        $serviciosIds = Servicio::where('id_alumno', $user->id_usuario)->pluck('id_servicio');
        $actividades = Actividad::with(['horas', 'servicio'])
            ->whereIn('id_servicio', $serviciosIds)
            ->where('estado', 'Aprobada')
            ->orderBy('fecha_limite', 'desc')
            ->get();

        // Preparar datos de horas (solo Adelantando)
        $datos = $actividades->map(function ($act) {
            $totalHoras = null;
            if ($act->servicio->tipo_servicio === 'Adelantando') {
                $totalMinutos = $this->calcularTotalMinutos($act);
                $horas = intdiv($totalMinutos, 60);
                $minutos = $totalMinutos % 60;
                $totalHoras = $horas . 'h ' . $minutos . 'm';
            }
            return [
                'id' => $act->id_actividad,
                'titulo' => $act->actividad,
                'fecha' => $act->fecha_limite,
                'tipo' => $act->servicio->tipo_servicio,
                'total_horas' => $totalHoras,
            ];
        });

        $lineasActividades = $actividades->pluck('actividad')->map(function ($t) {
            return '- ' . $t;
        })->implode("\n");

        $textoBase = "Por medio del presente, yo, {$user->nombre}, hago constar que he realizado las siguientes actividades de servicio social en el área asignada:\n\n";
        $textoEjemplo = "DESARROLLO Y MANTENIMIENTO\nDE PÁGINAS WEB PARA LOS\nLABORATORIOS DE\nCOMPUTACIÓN Y CÓMPUTO DE\nALTO DESEMPEÑO, ASÍ COMO EL\nMANTENIMIENTO PREVENTIVO Y\nCORRECTIVO DE SOFTWARE Y\nHARDWARE DE LAS ESTACIONES\nDE TRABAJO CON LAS QUE\nCUENTAN Y PROPORCIONAR\nSOPORTE TÉCNICO DEL ÁREA\nDE COMPUTACIÓN PARA LOS\nMIEMBROS DE LA COMUNIDAD\nDE LA FACULTAD DE INGENIERÍA\nELÉCTRICA";

        $textoSIIA = $textoBase
            . ($lineasActividades ? $lineasActividades : "- Sin actividades aprobadas")
            . "\n\nTexto sugerido:\n"
            . $textoEjemplo;

        return view('alumno.reporte', [
            'actividades' => $actividades,
            'datos' => $datos,
            'user' => $user,
            'textoSIIA' => $textoSIIA,
        ]);
    }
}
