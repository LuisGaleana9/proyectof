@extends('alumno.layout')

@section('content')
<div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
    <div>
        <h2 style="margin: 0;">{{ $reporte->nombreReporte() }}</h2>
        <p class="muted" style="margin: 0.25rem 0 0;">
            {{ $reporte->alumnoServicio->servicio->nombre ?? 'Servicio' }}
            · Entrega: {{ $reporte->fecha_entrega->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }}
        </p>
    </div>
    <a href="{{ route('alumno.reportes.index') }}" class="btn btn-secondary">Volver a reportes</a>
</div>

@if($reporte->estado === 'Corregir' && $reporte->correcciones)
    <div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
        <h3 style="margin-top: 0; color: #991b1b;">Correcciones solicitadas por tu profesor</h3>
        <div style="background-color: #fef2f2; padding: 1rem; border-radius: 0.375rem; white-space: pre-wrap;">{{ $reporte->correcciones }}</div>
        <p class="muted" style="margin-top: 0.5rem; margin-bottom: 0;">
            Revisado el {{ $reporte->fecha_revision->format('d/m/Y \\a \\l\\a\\s H:i') }}
        </p>
    </div>
@endif

<div class="card">
    @if($reporte->tipo === 'General')
        <div style="background-color: #fffbeb; border: 1px solid #f59e0b; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
            <strong>Reporte General de Servicio Social</strong>
            <p style="margin: 0.5rem 0 0;">
                Este es tu reporte final. Debes escribir un resumen general de toda tu experiencia durante el servicio social:
                actividades realizadas, habilidades adquiridas, dificultades encontradas y conclusiones.
            </p>
            <p style="margin: 0.5rem 0 0;">
                <strong>Periodo:</strong> del {{ $periodoInicio->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }} al {{ $periodoFin->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }}
            </p>
        </div>
    @else
        <div style="background-color: #eff6ff; border: 1px solid #3b82f6; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
            <strong>Reporte Bimestral #{{ $reporte->numero_reporte }}</strong>
            <p style="margin: 0.5rem 0 0;">
                Describe las actividades que realizaste durante este periodo bimestral.
            </p>
            <p style="margin: 0.5rem 0 0;">
                <strong>Periodo:</strong> del {{ $periodoInicio->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }} al {{ $periodoFin->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }}
            </p>
        </div>
    @endif

    @if($actividades->isNotEmpty())
        <div style="background-color: #f0fdf4; border: 1px solid #22c55e; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
            <strong>Actividades aprobadas en este periodo ({{ $actividades->count() }})</strong>
            <ul style="margin: 0.5rem 0 0; padding-left: 1.25rem;">
                @foreach($actividades as $act)
                    <li>{{ $act->actividad }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <div style="background-color: #fefce8; border: 1px solid #eab308; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
            <strong>No se encontraron actividades aprobadas en este periodo.</strong>
            <p style="margin: 0.5rem 0 0;">Puedes redactar tu reporte manualmente describiendo las actividades que realizaste.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('alumno.reportes.enviar', $reporte->id) }}">
        @csrf

        <label for="contenido">Contenido del reporte</label>
        <textarea
            id="contenido"
            name="contenido"
            rows="15"
            style="max-width: 100%; min-height: 300px;"
            placeholder="Escribe aquí el contenido de tu reporte..."
            required
        >{{ old('contenido', $reporte->contenido ?? $mensajePrellenado) }}</textarea>

        <div style="margin-top: 1rem; display: flex; gap: 1rem; align-items: center;">
            <button type="submit" class="btn" onclick="return confirm('¿Estás seguro de enviar tu reporte? Una vez enviado, tu profesor lo revisará.')">
                Enviar reporte
            </button>
            <a href="{{ route('alumno.reportes.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

        <p class="muted" style="margin-top: 0.75rem;">
            Al enviarlo, el profesor revisara tu reporte.
        </p>
    </form>
</div>
@endsection
