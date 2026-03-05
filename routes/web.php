<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ProfesorRevisionController;
use App\Http\Controllers\ProfesorActividadController;
use App\Http\Controllers\ReporteAlumnoController;
use App\Http\Controllers\ReporteProfesorController;
use App\Models\AlumnoServicio;
use App\Models\Actividad;
use App\Models\Reporte;
use Illuminate\Support\Facades\Auth;

// Rutas publicas del sistema
// La raiz redirige al formulario de inicio de sesion
Route::get('/', function () {
    return redirect()->route('login');
});

// Autenticacion basica
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);


// Rutas protegidas para usuarios autenticados
Route::middleware(['auth'])->group(function () {

    // Admin: panel y recursos base
    Route::get('/admin', function () {
        return view('admin.index');
    });

    Route::resource('/admin/profesores', ProfesorController::class);
    Route::resource('/admin/dependencias', DependenciaController::class);

    // Alumno: pagina principal y progreso
    Route::get('/usuario', function () {
        $user = Auth::user();

        // Obtener inscripciones del alumno
        $inscripciones = AlumnoServicio::where('id_alumno', $user->id_usuario)->get();

        // Actividades del alumno
        $actividades = Actividad::with(['servicio', 'horas', 'alumnoServicio'])
            ->where('estado', '!=', 'Rechazada')
            ->whereIn('id_alumno_servicio', $inscripciones->pluck('id'))
            ->orderBy('fecha_limite', 'asc')
            ->get();

        $actividades->each(function ($a) use ($inscripciones) {
            // Determinar tipo de servicio para este alumno
            $as = $inscripciones->firstWhere('id', $a->id_alumno_servicio);
            $tipoServicio = $as ? $as->tipo_servicio : 'Regular';
            $a->tipo_servicio_alumno = $tipoServicio;

            if ($tipoServicio === 'Adelantando' && $as) {
                $asId = $as->id;

                $horasAlumno = $a->horas->where('id_alumno_servicio', $asId);
                $a->total_minutos_calculados = (int) $horasAlumno->reduce(function ($carry, $h) {
                    if ($h->hora_final) {
                        $inicio = \Carbon\Carbon::parse($h->hora_inicio);
                        $fin = \Carbon\Carbon::parse($h->hora_final);
                        $carry += $inicio->diffInMinutes($fin);
                    }
                    return $carry;
                }, 0);
            }
        });

        $mostrarProgreso = $actividades->contains(function ($a) {
            return $a->tipo_servicio_alumno === 'Adelantando';
        });

        $totalMinutos = $actividades->filter(function ($a) {
            return $a->tipo_servicio_alumno === 'Adelantando' && $a->estado === 'Aprobada';
        })->sum(function ($a) {
            return $a->total_minutos_calculados ?? 0;
        });

        $totalHoras = intdiv($totalMinutos, 60);
        $minutosRestantes = $totalMinutos % 60;
        $metaHoras = 480;
        $totalHorasDec = $totalMinutos / 60;
        $porcentaje = $metaHoras > 0 ? min(100, round(($totalHorasDec / $metaHoras) * 100, 1)) : 0;

        // Obtener proximo reporte para alumnos Regular
        $proximoReporte = null;
        $inscripcionRegular = $inscripciones->firstWhere('tipo_servicio', 'Regular');
        if ($inscripcionRegular) {
            $proximoReporte = Reporte::where('id_alumno_servicio', $inscripcionRegular->id)
                ->whereIn('estado', ['Pendiente', 'Corregir'])
                ->orderBy('numero_reporte', 'asc')
                ->first();
        }

        return view('alumno.index', [
            'actividades' => $actividades,
            'totalHoras' => $totalHoras,
            'totalMinutos' => $minutosRestantes,
            'porcentaje' => $porcentaje,
            'metaHoras' => $metaHoras,
            'mostrarProgreso' => $mostrarProgreso,
            'proximoReporte' => $proximoReporte,
        ]);
    });

    // Alumno: flujo de actividades
    Route::get('/alumno/actividades/{id}', [ActividadController::class, 'show'])->name('alumno.actividad.show');
    Route::post('/alumno/actividades/{id}/check-in', [ActividadController::class, 'checkIn'])->name('alumno.actividad.checkin');
    Route::post('/alumno/actividades/{id}/check-out', [ActividadController::class, 'checkOut'])->name('alumno.actividad.checkout');
    Route::post('/alumno/actividades/{id}/realizada', [ActividadController::class, 'marcarRealizada'])->name('alumno.actividad.realizada');
    Route::post('/alumno/actividades/{id}/revision', [ActividadController::class, 'enviarRevision'])->name('alumno.actividad.revision');
    Route::post('/alumno/actividades/{id}/cancelar-revision', [ActividadController::class, 'cancelarRevision'])->name('alumno.actividad.cancelar');
    Route::get('/alumno/reporte', [ActividadController::class, 'reporte'])->name('alumno.reporte');

    // Alumno: reportes bimestrales de servicio social
    Route::get('/alumno/reportes', [ReporteAlumnoController::class, 'index'])->name('alumno.reportes.index');
    Route::get('/alumno/reportes/{id}/escribir', [ReporteAlumnoController::class, 'escribir'])->name('alumno.reportes.escribir');
    Route::post('/alumno/reportes/{id}/enviar', [ReporteAlumnoController::class, 'enviar'])->name('alumno.reportes.enviar');
    Route::get('/alumno/reportes/{id}/pdf', [ReporteAlumnoController::class, 'descargarPdf'])->name('alumno.reportes.pdf');

    // Profesor: panel principal
    Route::get('/profesor', function () {
        $profesorId = Auth::user()->id_usuario;
        $reportesPendientes = Reporte::where('estado', 'Enviado')
            ->whereHas('alumnoServicio.servicio', function ($q) use ($profesorId) {
                $q->where('id_profesor', $profesorId);
            })
            ->with(['alumnoServicio.alumno', 'alumnoServicio.servicio'])
            ->orderBy('fecha_entrega', 'asc')
            ->get();

        return view('profesor.index', [
            'reportesPendientes' => $reportesPendientes,
        ]);
    });
    Route::resource('/profesor/alumnos', AlumnoController::class);
    Route::resource('/profesor/servicios', ServicioController::class);

    // Profesor: gestion de actividades
    Route::get('/profesor/actividades', [ProfesorActividadController::class, 'index'])->name('profesor.actividades.index');
    Route::get('/profesor/actividades/crear', [ProfesorActividadController::class, 'create'])->name('profesor.actividades.create');
    Route::post('/profesor/actividades', [ProfesorActividadController::class, 'store'])->name('profesor.actividades.store');
    Route::get('/profesor/actividades/{id}/editar', [ProfesorActividadController::class, 'edit'])->name('profesor.actividades.edit');
    Route::put('/profesor/actividades/{id}', [ProfesorActividadController::class, 'update'])->name('profesor.actividades.update');
    Route::delete('/profesor/actividades/{id}', [ProfesorActividadController::class, 'destroy'])->name('profesor.actividades.destroy');

    // Profesor: revision y aprobacion de actividades
    Route::get('/profesor/revisiones', [ProfesorRevisionController::class, 'index'])->name('profesor.revisiones');
    Route::post('/profesor/revisiones/{id}/aprobar', [ProfesorRevisionController::class, 'aprobar'])->name('profesor.revisiones.aprobar');
    Route::post('/profesor/revisiones/{id}/rechazar', [ProfesorRevisionController::class, 'rechazar'])->name('profesor.revisiones.rechazar');
    Route::delete('/profesor/revisiones/horas/{idHora}', [ProfesorRevisionController::class, 'rechazarHora'])->name('profesor.revisiones.horas.rechazar');

    // Profesor: revision de reportes bimestrales
    Route::get('/profesor/reportes', [ReporteProfesorController::class, 'index'])->name('profesor.reportes.index');
    Route::get('/profesor/reportes/{id}', [ReporteProfesorController::class, 'revisar'])->name('profesor.reportes.revisar');
    Route::post('/profesor/reportes/{id}/aprobar', [ReporteProfesorController::class, 'aprobar'])->name('profesor.reportes.aprobar');
    Route::post('/profesor/reportes/{id}/rechazar', [ReporteProfesorController::class, 'rechazar'])->name('profesor.reportes.rechazar');
    Route::post('/profesor/reportes/{id}/corregir', [ReporteProfesorController::class, 'corregir'])->name('profesor.reportes.corregir');

});