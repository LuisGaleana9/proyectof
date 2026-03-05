<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Reporte;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Notificacion de reportes pendientes para profesores
        View::composer('profesor.layout', function ($view) {
            $count = 0;
            if (Auth::check() && Auth::user()->rol === 'Profesor') {
                $profesorId = Auth::user()->id_usuario;
                $count = Reporte::where('estado', 'Enviado')
                    ->whereHas('alumnoServicio.servicio', function ($q) use ($profesorId) {
                        $q->where('id_profesor', $profesorId);
                    })
                    ->count();
            }
            $view->with('reportesPendientesCount', $count);
        });
    }
}
