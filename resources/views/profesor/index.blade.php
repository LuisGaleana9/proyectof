@extends('profesor.layout')

@section('content')
    <div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
        <div>
            <h2 style="margin: 0;">Panel Académico</h2>
            <p class="muted" style="margin: 0.25rem 0 0;">Facultad de Ingeniería Eléctrica · UMICH</p>
        </div>
    </div>

    @if(isset($reportesPendientes) && $reportesPendientes->count() > 0)
        <div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid #f59e0b;">
            <h3 style="margin-top: 0;">Reportes pendientes de revisión</h3>
            <p class="muted" style="margin-bottom: 1rem;">Los siguientes alumnos han enviado reportes que necesitan tu revisión.</p>

            <table class="table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Reporte</th>
                        <th>Fecha límite SIIA</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportesPendientes as $reporte)
                        <tr>
                            <td>
                                {{ $reporte->alumnoServicio->alumno->nombre ?? '—' }}
                                {{ $reporte->alumnoServicio->alumno->apellidos_p ?? '' }}
                                <br><small class="muted">{{ $reporte->alumnoServicio->alumno->matricula ?? '' }}</small>
                            </td>
                            <td>
                                <strong>{{ $reporte->nombreReporte() }}</strong>
                                <br><small class="muted">{{ $reporte->alumnoServicio->servicio->nombre ?? '' }}</small>
                            </td>
                            <td>
                                {{ $reporte->fecha_entrega->format('d/m/Y') }}
                                @php
                                    $dias = now()->startOfDay()->diffInDays($reporte->fecha_entrega, false);
                                @endphp
                                @if($dias > 0 && $dias <= 5)
                                    <br><small style="color: #92400e;">{{ $dias }} días restantes</small>
                                @elseif($dias <= 0)
                                    <br><small style="color: #991b1b;">Vencido</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('profesor.reportes.revisar', $reporte->id) }}" class="btn btn-secondary" style="font-size: 0.8rem;">Revisar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection