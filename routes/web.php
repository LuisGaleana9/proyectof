<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ActividadAlumnoController;
use App\Http\Controllers\ActividadProfesorController;

//Rutas publicas
// Desde la raiz, redirige a login
Route::get('/', function () {
    return redirect()->route('login');
});

// Actividades controladores ya existentes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);


//Rutas protegidas
Route::middleware(['auth'])->group(function () {

    // Admin
    Route::get('/admin', function () {
        return view('admin.index');
    });

    Route::resource('/admin/profesores', ProfesorController::class);
    Route::resource('/admin/dependencias', DependenciaController::class);

    // Alumno
    Route::get('/usuario', function () {
        return view('alumno.index');
    });
    // Actividades del alumno
    Route::get('/usuario/actividades', [ActividadAlumnoController::class, 'index'])->name('alumno.actividades.index');
    Route::post('/usuario/actividades/{id}/start', [ActividadAlumnoController::class, 'start'])->name('alumno.actividades.start');
    Route::post('/usuario/actividades/{id}/stop', [ActividadAlumnoController::class, 'stop'])->name('alumno.actividades.stop');
    Route::post('/usuario/actividades/{id}/completar', [ActividadAlumnoController::class, 'completar'])->name('alumno.actividades.completar');
    Route::get('/usuario/actividades/informe', [ActividadAlumnoController::class, 'informe'])->name('alumno.actividades.informe');

    // Profesor
    Route::get('/profesor', function () {

    // Rutas de actividades ya definidas más abajo para profesor
        return view('profesor.index');
    });
    Route::resource('/profesor/alumnos', AlumnoController::class);
    Route::resource('/profesor/servicios', ServicioController::class);
    // Actividades del profesor para revisión
    Route::get('/profesor/actividades', [ActividadProfesorController::class, 'index'])->name('profesor.actividades.index');
    Route::post('/profesor/actividades/{id}/aprobar', [ActividadProfesorController::class, 'aprobar'])->name('profesor.actividades.aprobar');
    Route::post('/profesor/actividades/{id}/rechazar', [ActividadProfesorController::class, 'rechazar'])->name('profesor.actividades.rechazar');
    Route::post('/profesor/actividades/{id}/regresar', [ActividadProfesorController::class, 'regresar'])->name('profesor.actividades.regresar');
    Route::post('/profesor/actividades/{id}/cancelar', [ActividadProfesorController::class, 'cancelar'])->name('profesor.actividades.cancelar');

    // Actividades por alumno (interfaz de alumno desde profesor)
    Route::get('/profesor/alumnos/{alumnoId}/actividades', [ActividadProfesorController::class, 'alumno'])->name('profesor.alumnos.actividades.index');
    Route::get('/profesor/alumnos/{alumnoId}/actividades/crear', [ActividadProfesorController::class, 'crear'])->name('profesor.alumnos.actividades.crear');
    Route::post('/profesor/alumnos/{alumnoId}/actividades', [ActividadProfesorController::class, 'guardar'])->name('profesor.alumnos.actividades.guardar');

});