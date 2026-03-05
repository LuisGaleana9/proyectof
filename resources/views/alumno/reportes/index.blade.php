@extends('alumno.layout')

@section('content')
<div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
    <div>
        <h2 style="margin: 0;">Reportes de Servicio Social</h2>
        <p class="muted" style="margin: 0.25rem 0 0;">Reportes bimestrales · {{ $user->nombre }} {{ $user->apellidos_p }}</p>
    </div>
</div>

@if($proximoReporte)
    <div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid var(--primary-color);">
        <h3 style="margin-top: 0;">Próximo reporte</h3>
        <p style="margin: 0.25rem 0;">
            <strong>{{ $proximoReporte->nombreReporte() }}</strong>
            — Fecha de entrega: <strong>{{ $proximoReporte->fecha_entrega->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }}</strong>
        </p>
        @php
            $diasRestantes = now()->startOfDay()->diffInDays($proximoReporte->fecha_entrega, false);
            $puedeEscribir = $proximoReporte->puedeEscribir();
        @endphp

        @if($proximoReporte->estado === 'Corregir')
            <div class="alert alert-danger" style="margin-top: 0.75rem;">
                <strong>Tu reporte necesita correcciones.</strong> Revisa las observaciones de tu profesor y vuelve a enviarlo.
            </div>
        @endif

        @if($diasRestantes > 10)
            <p class="muted">
                Podrás escribirlo a partir del <strong>{{ $proximoReporte->fechaApertura()->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }}</strong>
            </p>
        @elseif($diasRestantes > 0)
            <p style="color: #166534;">
                <strong>Puedes escribir tu reporte.</strong> Tienes {{ $diasRestantes }} días para entregarlo.
            </p>
        @elseif($diasRestantes == 0)
            <p style="color: #92400e;">
                <strong>Hoy es el dia limite.</strong> Envía tu reporte hoy mismo.
            </p>
        @else
            <p style="color: #991b1b;">
                <strong>Fecha límite vencida.</strong>
            </p>
        @endif

        @if($puedeEscribir)
            <a href="{{ route('alumno.reportes.escribir', $proximoReporte->id) }}" class="btn" style="margin-top: 0.5rem;">
                {{ $proximoReporte->estado === 'Corregir' ? 'Corregir y reenviar reporte' : 'Escribir reporte' }}
            </a>
        @elseif(!$proximoReporte->reporteAnteriorAprobado())
            <p class="muted" style="margin-top: 0.5rem;">
                Primero debes tener aprobado tu reporte anterior.
            </p>
        @endif
    </div>
@endif

@foreach($inscripciones as $inscripcion)
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="margin-top: 0;">{{ $inscripcion->servicio->nombre ?? 'Servicio' }}</h3>
        <p class="muted" style="margin-bottom: 0.5rem;">
            Inicio: {{ $inscripcion->fecha_inicio->format('d/m/Y') }}
            · Tipo: <span class="badge badge-success">Regular</span>
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Fecha de entrega</th>
                    <th>Fecha de apertura</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inscripcion->reportes as $reporte)
                    @php
                        $puedeEscribir = $reporte->puedeEscribir();
                        $badgeClass = match($reporte->estado) {
                            'Aprobado' => 'badge-success',
                            'Enviado' => 'badge-info',
                            'Corregir' => 'badge-warning',
                            'Rechazado' => 'badge-danger',
                            default => '',
                        };
                    @endphp
                    <tr>
                        <td>{{ $reporte->numero_reporte }}</td>
                        <td>
                            <span class="badge {{ $reporte->tipo === 'General' ? 'badge-warning' : 'badge-info' }}">
                                {{ $reporte->tipo }}
                            </span>
                        </td>
                        <td>{{ $reporte->fecha_entrega->format('d/m/Y') }}</td>
                        <td>{{ $reporte->fechaApertura()->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $reporte->estado }}</span>
                        </td>
                        <td>
                            <div class="actions">
                                @if($puedeEscribir)
                                    <a href="{{ route('alumno.reportes.escribir', $reporte->id) }}" class="btn btn-secondary" style="font-size: 0.8rem;">
                                        {{ $reporte->estado === 'Corregir' ? 'Corregir' : 'Escribir' }}
                                    </a>
                                @endif

                                @if($reporte->estado === 'Aprobado')
                                    <a href="{{ route('alumno.reportes.pdf', $reporte->id) }}" class="btn" style="font-size: 0.8rem;">
                                        Descargar PDF
                                    </a>
                                @endif

                                @if($reporte->estado === 'Enviado')
                                    <span class="muted" style="font-size: 0.8rem;">En revisión por tu profesor</span>
                                @endif

                                @if($reporte->estado === 'Rechazado')
                                    <span class="muted" style="font-size: 0.8rem; color: #991b1b;">Reporte rechazado</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Mostrar recordatorios para reportes aprobados --}}
        @foreach($inscripcion->reportes->where('estado', 'Aprobado') as $reporte)
            @php
                $diasParaEntrega = now()->startOfDay()->diffInDays($reporte->fecha_entrega, false);
            @endphp
            @if($diasParaEntrega >= 0)
                <div class="alert alert-success" style="margin-top: 0.75rem;">
                    <strong>{{ $reporte->nombreReporte() }} aprobado.</strong>
                    Recuerda enviarlo al SIIA antes del {{ $reporte->fecha_entrega->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }}
                    @if($diasParaEntrega > 0)
                        ({{ $diasParaEntrega }} días restantes).
                    @else
                        (¡hoy es la fecha límite!).
                    @endif
                </div>
            @endif
        @endforeach
    </div>
@endforeach

@if($inscripciones->isEmpty())
    <div class="card">
        <p>No tienes inscripciones de servicio social tipo Regular con reportes programados.</p>
    </div>
@endif
@endsection
