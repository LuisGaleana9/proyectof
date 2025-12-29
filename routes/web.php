<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DependenciaController;

// -- Rutas publicas --

// Desde la raiz, redirige a login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);


// -- Rutas protegidas --

Route::middleware(['auth'])->group(function () {

    // Panel de control
    Route::get('/admin', function () {
        return "<h1>Bienvenido Admin</h1>";
    });
    Route::get('/usuario', function () {
        return "<h1>Perfil de Alumno</h1>";
    });
    Route::get('/profesor', function () {
        return "<h1>Perfil de Profesor</h1>";
    });

});