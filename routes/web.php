<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ProfesorRevisionController;
use App\Http\Controllers\ProfesorActividadController;
use App\Models\Servicio;
use App\Models\Actividad;
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
        $serviciosIds = Servicio::where('id_alumno', $user->id_usuario)->pluck('id_servicio');
        $actividades = Actividad::with(['servicio', 'horas'])
            ->whereIn('id_servicio', $serviciosIds)
            ->where('estado', '!=', 'Rechazada')
            ->orderBy('fecha_limite', 'asc')
            ->get();

        $actividades->each(function ($a) {
            if ($a->servicio->tipo_servicio === 'Adelantando') {
                if (\Illuminate\Support\Facades\Schema::hasColumn('horas', 'horas_totales')) {
                    $horas = (float) $a->horas->sum('horas_totales');
                    $a->total_minutos_calculados = (int) round($horas * 60);
                } else {
                    $a->total_minutos_calculados = $a->horas->reduce(function ($carry, $h) {
                        if ($h->hora_final) {
                            $inicio = \Carbon\Carbon::parse($h->hora_inicio);
                            $fin = \Carbon\Carbon::parse($h->hora_final);
                            $carry += $inicio->diffInMinutes($fin);
                        }
                        return $carry;
                    }, 0);
                }
            }
        });

        $mostrarProgreso = $actividades->contains(function ($a) {
            return $a->servicio->tipo_servicio === 'Adelantando';
        });

        $totalMinutos = $actividades->filter(function ($a) {
            return $a->servicio->tipo_servicio === 'Adelantando' && $a->estado === 'Aprobada';
        })->sum(function ($a) {
            return $a->total_minutos_calculados ?? 0;
        });

        $totalHoras = intdiv($totalMinutos, 60);
        $minutosRestantes = $totalMinutos % 60;
        $metaHoras = 480;
        $totalHorasDec = $totalMinutos / 60;
        $porcentaje = $metaHoras > 0 ? min(100, round(($totalHorasDec / $metaHoras) * 100, 1)) : 0;

        return view('alumno.index', [
            'actividades' => $actividades,
            'totalHoras' => $totalHoras,
            'totalMinutos' => $minutosRestantes,
            'porcentaje' => $porcentaje,
            'metaHoras' => $metaHoras,
            'mostrarProgreso' => $mostrarProgreso,
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

    // Profesor: panel principal
    Route::get('/profesor', function () {
        return view('profesor.index');
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

});