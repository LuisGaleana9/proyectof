@extends('profesor.layout')

@section('content')
<div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
    <div>
        <h2 style="margin: 0;">Revisar {{ $reporte->nombreReporte() }}</h2>
        <p class="muted" style="margin: 0.25rem 0 0;">
            {{ $reporte->alumnoServicio->alumno->nombre ?? '' }}
            {{ $reporte->alumnoServicio->alumno->apellidos_p ?? '' }}
            · {{ $reporte->alumnoServicio->servicio->nombre ?? '' }}
        </p>
    </div>
    <a href="{{ route('profesor.reportes.index') }}" class="btn btn-secondary">Volver a reportes</a>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="grid-2" style="margin-bottom: 1rem;">
        <div>
            <p class="muted" style="margin: 0;">Alumno</p>
            <p style="margin: 0.25rem 0;">
                <strong>{{ $reporte->alumnoServicio->alumno->nombre ?? '' }}
                {{ $reporte->alumnoServicio->alumno->apellidos_p ?? '' }}
                {{ $reporte->alumnoServicio->alumno->apellidos_m ?? '' }}</strong>
            </p>
            <p class="muted" style="margin: 0;">Matrícula: {{ $reporte->alumnoServicio->alumno->matricula ?? '' }}</p>
        </div>
        <div>
            <p class="muted" style="margin: 0;">Servicio</p>
            <p style="margin: 0.25rem 0;"><strong>{{ $reporte->alumnoServicio->servicio->nombre ?? '' }}</strong></p>
            <p class="muted" style="margin: 0;">Inicio: {{ $reporte->alumnoServicio->fecha_inicio ? $reporte->alumnoServicio->fecha_inicio->format('d/m/Y') : '—' }}</p>
        </div>
    </div>
    <div class="grid-2">
        <div>
            <p class="muted" style="margin: 0;">Reporte</p>
            <p style="margin: 0.25rem 0;">
                <strong>#{{ $reporte->numero_reporte }}</strong> ·
                <span class="badge {{ $reporte->tipo === 'General' ? 'badge-warning' : 'badge-info' }}">{{ $reporte->tipo }}</span>
            </p>
        </div>
        <div>
            <p class="muted" style="margin: 0;">Fecha límite SIIA</p>
            <p style="margin: 0.25rem 0;"><strong>{{ $reporte->fecha_entrega->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }}</strong></p>
            @php
                $diasRestantes = now()->startOfDay()->diffInDays($reporte->fecha_entrega, false);
            @endphp
            @if($diasRestantes > 0)
                <p class="muted" style="margin: 0;">{{ $diasRestantes }} días restantes</p>
            @elseif($diasRestantes == 0)
                <p style="margin: 0; color: #92400e;"><strong>¡Hoy vence!</strong></p>
            @else
                <p style="margin: 0; color: #991b1b;"><strong>Vencido hace {{ abs($diasRestantes) }} días</strong></p>
            @endif
        </div>
    </div>
    <div style="margin-top: 0.5rem;">
        <p class="muted" style="margin: 0;">Enviado el</p>
        <p style="margin: 0.25rem 0;">{{ $reporte->fecha_envio ? $reporte->fecha_envio->format('d/m/Y \\a \\l\\a\\s H:i') : '—' }}</p>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <h3 style="margin-top: 0;">Contenido del reporte</h3>
    <div style="background-color: #f9fafb; padding: 1.5rem; border-radius: 0.375rem; border: 1px solid var(--border-color); white-space: pre-wrap; line-height: 1.7;">{{ $reporte->contenido }}</div>
</div>

@if($reporte->estado === 'Enviado')
    <div class="card">
        <h3 style="margin-top: 0;">Acciones de revisión</h3>
        <p class="muted" style="margin-bottom: 1rem;">
            Selecciona una acción para este reporte. Recuerda que debe ser enviado al SIIA antes del {{ $reporte->fecha_entrega->format('d/m/Y') }}.
        </p>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-start;">
            {{-- Aprobar --}}
            <form method="POST" action="{{ route('profesor.reportes.aprobar', $reporte->id) }}"
                  onsubmit="return confirm('¿Aprobar este reporte? El alumno podrá descargarlo como PDF.')">
                @csrf
                <button type="submit" class="btn" style="background-color: #16a34a;">Aprobar reporte</button>
            </form>

            {{-- Rechazar --}}
            <form method="POST" action="{{ route('profesor.reportes.rechazar', $reporte->id) }}"
                  onsubmit="return confirm('¿Rechazar este reporte? Esta acción indicará al alumno que su reporte fue rechazado.')">
                @csrf
                <button type="submit" class="btn btn-danger">Rechazar reporte</button>
            </form>
        </div>

        {{-- Corregir --}}
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <h4 style="margin-top: 0;">Solicitar correcciones</h4>
            <form method="POST" action="{{ route('profesor.reportes.corregir', $reporte->id) }}">
                @csrf
                <label for="correcciones">Correcciones que esperas del alumno</label>
                <textarea
                    id="correcciones"
                    name="correcciones"
                    rows="6"
                    style="max-width: 100%;"
                    placeholder="Escribe aquí todas las correcciones que necesita hacer el alumno en su reporte..."
                    required
                ></textarea>
                <button type="submit" class="btn" style="background-color: #d97706;"
                        onclick="return confirm('¿Enviar correcciones al alumno?')">
                    Enviar correcciones
                </button>
            </form>
        </div>
    </div>
@else
    <div class="card">
        <p class="muted">Este reporte tiene estado: <span class="badge">{{ $reporte->estado }}</span> y no puede ser revisado.</p>
    </div>
@endif
@endsection
