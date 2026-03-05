@extends('profesor.layout')

@section('content')
<div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
    <div>
        <h2 style="margin: 0;">Reportes de Servicio Social</h2>
        <p class="muted" style="margin: 0.25rem 0 0;">Reportes bimestrales enviados por alumnos para revisión.</p>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Servicio</th>
                    <th>Reporte</th>
                    <th>Tipo</th>
                    <th>Fecha envío</th>
                    <th>Fecha límite SIIA</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportes as $reporte)
                    <tr>
                        <td>
                            {{ $reporte->alumnoServicio->alumno->nombre ?? '—' }}
                            {{ $reporte->alumnoServicio->alumno->apellidos_p ?? '' }}
                            <br><small class="muted">{{ $reporte->alumnoServicio->alumno->matricula ?? '' }}</small>
                        </td>
                        <td>{{ $reporte->alumnoServicio->servicio->nombre ?? '—' }}</td>
                        <td><strong>#{{ $reporte->numero_reporte }}</strong></td>
                        <td>
                            <span class="badge {{ $reporte->tipo === 'General' ? 'badge-warning' : 'badge-info' }}">
                                {{ $reporte->tipo }}
                            </span>
                        </td>
                        <td>{{ $reporte->fecha_envio ? $reporte->fecha_envio->format('d/m/Y H:i') : '—' }}</td>
                        <td>
                            {{ $reporte->fecha_entrega->format('d/m/Y') }}
                            @php
                                $diasRestantes = now()->startOfDay()->diffInDays($reporte->fecha_entrega, false);
                            @endphp
                            @if($diasRestantes > 0 && $diasRestantes <= 5)
                                <br><small style="color: #92400e;">{{ $diasRestantes }} días restantes</small>
                            @elseif($diasRestantes <= 0)
                                <br><small style="color: #991b1b;">Vencido</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('profesor.reportes.revisar', $reporte->id) }}" class="btn btn-secondary" style="font-size: 0.8rem;">
                                Revisar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No hay reportes pendientes de revisión.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
