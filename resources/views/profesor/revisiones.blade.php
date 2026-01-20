@extends('profesor.layout')

@section('content')
<div class="card">
    <div class="actions" style="justify-content: space-between;">
        <div>
            <h2 style="margin: 0;">Actividades en revisión</h2>
            <p class="muted" style="margin: 0.25rem 0 0;">Aprueba las actividades terminadas por alumnos.</p>
        </div>
    </div>

    <div style="margin-top: 1rem;">
        <table class="table">
            <thead>
                <tr>
                    <th>Actividad</th>
                    <th>Alumno</th>
                    <th>Tipo</th>
                    <th>Fecha límite</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividades as $a)
                    <tr>
                        <td>{{ $a->actividad }}</td>
                        <td>{{ $a->servicio->alumno->nombre ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $a->servicio->tipo_servicio === 'Adelantando' ? 'badge-info' : 'badge-success' }}">
                                {{ $a->servicio->tipo_servicio }}
                            </span>
                        </td>
                        <td>{{ $a->fecha_limite }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('profesor.revisiones.aprobar', $a->id_actividad) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Aprobar</button>
                                </form>
                                <form method="POST" action="{{ route('profesor.revisiones.rechazar', $a->id_actividad) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Rechazar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No hay actividades en revisión.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
