<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\AlumnoServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ActividadController extends Controller
{
    // Calcular minutos acumulados por actividad (para un alumno especifico)
    private function calcularTotalMinutos(Actividad $actividad, $alumnoServicioId = null): int
    {
        $horas = $actividad->horas;

        // Si se pasa un alumnoServicioId, filtrar solo las horas de ese alumno
        if ($alumnoServicioId) {
            $horas = $horas->where('id_alumno_servicio', $alumnoServicioId);
        }

        $total = (float) $horas->sum('horas_totales');
        return (int) round($total * 60);
    }

    // Obtener la inscripcion activa del alumno para una actividad
    private function getAlumnoServicio(Actividad $actividad)
    {
        $user = Auth::user();

        $as = AlumnoServicio::where('id', $actividad->id_alumno_servicio)
            ->where('id_alumno', $user->id_usuario)
            ->first();

        if (!$as) {
            abort(403);
        }

        return $as;
    }

    // Mostrar detalle de actividad para alumno
    public function show($id)
    {
        $actividad = Actividad::with(['servicio', 'horas'])->findOrFail($id);

        $alumnoServicio = $this->getAlumnoServicio($actividad);

        $tipoServicio = $alumnoServicio->tipo_servicio;

        // Filtrar horas del alumno actual
        $horasAlumno = $actividad->horas->where('id_alumno_servicio', $alumnoServicio->id);
        $horaAbierta = $horasAlumno->whereNull('hora_final')->sortByDesc('hora_inicio')->first();

        $totalMinutos = null;
        if ($tipoServicio === 'Adelantando') {
            $totalMinutos = $this->calcularTotalMinutos($actividad, $alumnoServicio->id);
        }

        return view('alumno.actividad', [
            'actividad' => $actividad,
            'tipoServicio' => $tipoServicio,
            'horaAbierta' => $horaAbierta,
            'totalMinutos' => $totalMinutos,
            'alumnoServicio' => $alumnoServicio,
            'horasAlumno' => $horasAlumno,
        ]);
    }

    // Registrar entrada de horas (Adelantando)
    public function checkIn($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $alumnoServicio = $this->getAlumnoServicio($actividad);

        if ($alumnoServicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['No disponible para tipo Regular.']);
        }

        if ($actividad->estado !== 'Activa') {
            return back()->withErrors(['La actividad no está activa para registrar horas.']);
        }

        $horaAbierta = $actividad->horas()
            ->where('id_alumno_servicio', $alumnoServicio->id)
            ->whereNull('hora_final')
            ->first();

        if ($horaAbierta) {
            return back()->withErrors(['Ya existe un registro de horas abierto.']);
        }

        $actividad->horas()->create([
            'hora_inicio' => now(),
            'asistencia' => 'Aprobada',
            'id_alumno_servicio' => $alumnoServicio->id,
        ]);

        return back()->with('status', 'Check-in registrado.');
    }

    // Registrar salida y cerrar horas (Adelantando)
    public function checkOut($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $alumnoServicio = $this->getAlumnoServicio($actividad);

        if ($alumnoServicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['No disponible para tipo Regular.']);
        }

        $horaAbierta = $actividad->horas()
            ->where('id_alumno_servicio', $alumnoServicio->id)
            ->whereNull('hora_final')
            ->latest('hora_inicio')
            ->first();

        if (!$horaAbierta) {
            return back()->withErrors(['No hay registro de horas abierto.']);
        }

        $horaAbierta->hora_final = now();

        $inicio = Carbon::parse($horaAbierta->hora_inicio);
        $fin = Carbon::parse($horaAbierta->hora_final);
        $diffMinutes = $inicio->diffInMinutes($fin);
        $horasDecimales = round($diffMinutes / 60, 2);

        $horaAbierta->horas_totales = $horasDecimales;

        $horaAbierta->save();

        return back()->with('status', 'Check-out registrado.');
    }

    // Marcar actividad como realizada (Regular)
    public function marcarRealizada($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $alumnoServicio = $this->getAlumnoServicio($actividad);

        if ($alumnoServicio->tipo_servicio !== 'Regular') {
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
        $alumnoServicio = $this->getAlumnoServicio($actividad);

        if ($alumnoServicio->tipo_servicio !== 'Adelantando') {
            return back()->withErrors(['Solo disponible para tipo Adelantando.']);
        }

        if ($actividad->estado !== 'Activa') {
            return back()->withErrors(['La actividad ya no está activa para enviarse a revisión.']);
        }

        $horaAbierta = $actividad->horas()
            ->where('id_alumno_servicio', $alumnoServicio->id)
            ->whereNull('hora_final')
            ->first();

        if ($horaAbierta) {
            return back()->withErrors(['No puedes enviar a revisión con una hora abierta.']);
        }

        $actividad->estado = 'En Revisión';
        $actividad->save();

        $totalMinutos = $this->calcularTotalMinutos($actividad, $alumnoServicio->id);
        $horas = intdiv($totalMinutos, 60);
        $minutos = $totalMinutos % 60;

        return back()->with('status', 'Actividad enviada a revisión. Total: ' . $horas . 'h ' . $minutos . 'm');
    }

    // Cancelar revision y volver a activa
    public function cancelarRevision($id)
    {
        $actividad = Actividad::with('servicio')->findOrFail($id);
        $this->getAlumnoServicio($actividad);

        return back()->withErrors(['Una actividad en revisión ya no puede ser cancelada por el alumno.']);
    }

    // Reporte del alumno con actividades aprobadas
    public function reporte(Request $request)
    {
        $user = Auth::user();

        // Obtener las inscripciones del alumno
        $inscripciones = AlumnoServicio::where('id_alumno', $user->id_usuario)->get();
        $serviciosIds = $inscripciones->pluck('id_servicio');
        $inscripcionesMap = $inscripciones->keyBy('id_servicio');

        // Actividades aprobadas del alumno
        $actividades = Actividad::with(['horas', 'servicio', 'alumnoServicio'])
            ->where('estado', 'Aprobada')
            ->whereIn('id_alumno_servicio', $inscripciones->pluck('id'))
            ->orderBy('fecha_limite', 'desc')
            ->get();

        // Preparar datos de horas
        $datos = $actividades->map(function ($act) use ($inscripciones) {
            $as = $inscripciones->firstWhere('id', $act->id_alumno_servicio);
            $tipoServicio = $as ? $as->tipo_servicio : 'Regular';

            $totalHoras = null;
            if ($tipoServicio === 'Adelantando') {
                $horasAlumno = $act->horas->where('id_alumno_servicio', $act->id_alumno_servicio);
                $totalMinutos = (int) $horasAlumno->reduce(function ($carry, $h) {
                    if ($h->hora_final) {
                        $inicio = Carbon::parse($h->hora_inicio);
                        $fin = Carbon::parse($h->hora_final);
                        $carry += $inicio->diffInMinutes($fin);
                    }
                    return $carry;
                }, 0);
                $horas = intdiv($totalMinutos, 60);
                $minutos = $totalMinutos % 60;
                $totalHoras = $horas . 'h ' . $minutos . 'm';
            }

            return [
                'id' => $act->id_actividad,
                'titulo' => $act->actividad,
                'fecha' => $act->fecha_limite,
                'tipo' => $tipoServicio,
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
