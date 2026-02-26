@extends('alumno.layout')

@section('content')
    <div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
        <div>
            <h2 style="margin: 0;">Portal del Alumno</h2>
            <p class="muted" style="margin: 0.25rem 0 0;">Facultad de Ingeniería Eléctrica · UMICH</p>
        </div>
    </div>

    <div class="card">
        @if($mostrarProgreso)
            <h3 style="margin-top: 0;">Progreso de horas</h3>
            <p class="muted" style="margin-bottom: 0.25rem;">Servicio social (Adelantando)</p>
            <p><strong>Acumulado:</strong> {{ $totalHoras }}h {{ $totalMinutos }}m</p>
            <p><strong>Requisito:</strong> {{ $metaHoras }}h · <strong>Avance:</strong> {{ $porcentaje }}%</p>
        @endif

        @php
            $activas = $actividades->where('estado', '!=', 'Aprobada');
            $aprobadas = $actividades->where('estado', 'Aprobada');
        @endphp

        <h3 style="margin-top: 0;">Actividades asignadas</h3>
        @if($activas->isEmpty())
            <p>No hay actividades activas.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activas as $a)
                        <tr>
                            <td>{{ $a->actividad }}</td>
                            <td>
                                <span class="badge {{ ($a->tipo_servicio_alumno ?? 'Regular') === 'Adelantando' ? 'badge-info' : 'badge-success' }}">
                                    {{ $a->tipo_servicio_alumno ?? 'Regular' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $a->estado === 'Aprobada' ? 'badge-success' : ($a->estado === 'En Revisión' ? 'badge-warning' : 'badge-info') }}">
                                    {{ $a->estado }}
                                </span>
                            </td>
                            <td>{{ $a->fecha_limite }}</td>
                            <td>
                                <a class="btn btn-secondary" href="{{ route('alumno.actividad.show', $a->id_actividad) }}">Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($aprobadas->isNotEmpty())
            <div style="margin-top: 1.5rem;"></div>
            <h3>Archivadas (Aprobadas)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Tipo</th>
                        <th>Fecha límite</th>
                        @if($mostrarProgreso)
                            <th>Total horas</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($aprobadas as $a)
                        <tr>
                            <td>{{ $a->actividad }}</td>
                            <td>
                                <span class="badge {{ ($a->tipo_servicio_alumno ?? 'Regular') === 'Adelantando' ? 'badge-info' : 'badge-success' }}">
                                    {{ $a->tipo_servicio_alumno ?? 'Regular' }}
                                </span>
                            </td>
                            <td>{{ $a->fecha_limite }}</td>
                            @if($mostrarProgreso)
                                <td>
                                    @php
                                        $mins = $a->total_minutos_calculados ?? 0;
                                        $h = intdiv($mins, 60);
                                        $m = $mins % 60;
                                    @endphp
                                    {{ $h }}h {{ $m }}m
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection