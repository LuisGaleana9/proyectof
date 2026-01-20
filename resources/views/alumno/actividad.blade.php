@extends('alumno.layout')

@section('content')
<div class="card">
    <div class="actions" style="justify-content: space-between;">
        <div>
            <h2 style="margin: 0;">Actividad</h2>
            <p class="muted" style="margin: 0.25rem 0 0;">Seguimiento de actividad.</p>
        </div>
        <a class="btn btn-secondary" href="{{ url('/usuario') }}">Volver</a>
    </div>

    <div style="margin-top: 1rem;">
        <strong>Título:</strong> {{ $actividad->actividad }}<br>
        <strong>Estado:</strong>
        <span class="badge {{ $actividad->estado === 'Aprobada' ? 'badge-success' : ($actividad->estado === 'En Revisión' ? 'badge-warning' : 'badge-info') }}">
            {{ $actividad->estado }}
        </span>
        <br>
        <strong>Fecha límite:</strong> {{ $actividad->fecha_limite }}<br>
        <strong>Tipo de servicio:</strong>
        <span class="badge {{ $tipoServicio === 'Adelantando' ? 'badge-info' : 'badge-success' }}">{{ $tipoServicio }}</span>
        @if($tipoServicio === 'Adelantando')
            <br>
            @php
                $mins = $totalMinutos ?? 0;
                $h = intdiv($mins, 60);
                $m = $mins % 60;
            @endphp
            <strong>Horas acumuladas:</strong> {{ $h }}h {{ $m }}m
        @endif
        @if(!empty($actividad->comentarios))
            <br>
            <strong>Comentarios:</strong> {{ $actividad->comentarios }}
        @endif
    </div>

    <hr style="margin: 1.5rem 0;">

    @if($tipoServicio === 'Adelantando')
        <h4>Registro de horas</h4>
        @if($actividad->estado !== 'Activa')
            <p class="muted">No se pueden registrar horas. Estado actual: {{ $actividad->estado }}</p>
            @if($actividad->estado === 'En Revisión')
                <form method="POST" action="{{ route('alumno.actividad.cancelar', $actividad->id_actividad) }}" style="margin-top: 0.5rem;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Cancelar revisión</button>
                </form>
            @endif
        @else
            <div class="actions" style="margin-bottom: 1rem;">
                @if($horaAbierta)
                    <form method="POST" action="{{ route('alumno.actividad.checkout', $actividad->id_actividad) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning">Check-out</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('alumno.actividad.checkin', $actividad->id_actividad) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Check-in</button>
                    </form>
                @endif
            </div>
        @endif

        <div>
            <h5>Historial</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Total (h)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actividad->horas as $h)
                        <tr>
                            <td>{{ $h->hora_inicio }}</td>
                            <td>{{ $h->hora_final ?? '—' }}</td>
                            <td>{{ $h->horas_totales ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Aún no hay registros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($actividad->estado === 'Activa')
            <div style="margin-top: 1rem;">
                <form method="POST" action="{{ route('alumno.actividad.revision', $actividad->id_actividad) }}">
                    @csrf
                    <button type="submit" class="btn btn-success" {{ $horaAbierta ? 'disabled' : '' }}>Enviar a revisión</button>
                </form>
            </div>
        @endif
    @else
        <h4>Acciones</h4>
        <p class="muted">Este tipo de servicio no requiere registro de horas.</p>
        @if($actividad->estado === 'Activa')
            <form method="POST" action="{{ route('alumno.actividad.realizada', $actividad->id_actividad) }}">
                @csrf
                <button type="submit" class="btn btn-success">Marcar como realizada</button>
            </form>
        @elseif($actividad->estado === 'En Revisión')
            <form method="POST" action="{{ route('alumno.actividad.cancelar', $actividad->id_actividad) }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Cancelar revisión</button>
            </form>
        @endif
    @endif

</div>
@endsection
