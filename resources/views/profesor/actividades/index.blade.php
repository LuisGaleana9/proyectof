@extends('profesor.layout')

@section('content')
    <div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
        <div>
            <h1 style="margin: 0;">Actividades asignadas</h1>
            <p class="muted" style="margin: 0.25rem 0 0;">Lista de tareas creadas para tus alumnos.</p>
        </div>
        <a href="{{ route('profesor.actividades.create') }}" class="btn">Asignar actividad</a>
    </div>

    <div class="card">
        @php
            $activas = $actividades->where('estado', '!=', 'Aprobada');
            $aprobadas = $actividades->where('estado', 'Aprobada');
        @endphp

        @if($activas->isEmpty())
            <p>No hay actividades activas.</p>
        @else
            <h3>Activas / En revisión</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Alumno</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Fecha límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activas as $a)
                        <tr>
                            <td>{{ $a->actividad }}</td>
                            <td>{{ $a->alumnoServicio->alumno->nombre ?? '—' }}</td>
                            <td>{{ $a->servicio->nombre ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $a->estado === 'Aprobada' ? 'badge-success' : ($a->estado === 'En Revisión' ? 'badge-warning' : 'badge-info') }}">
                                    {{ $a->estado }}
                                </span>
                            </td>
                            <td>{{ $a->fecha_limite }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-secondary" href="{{ route('profesor.actividades.edit', $a->id_actividad) }}">Editar</a>
                                    <form action="{{ route('profesor.actividades.destroy', $a->id_actividad) }}" method="POST" onsubmit="return confirm('¿Eliminar actividad?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                    </form>
                                </div>
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
                        <th>Alumno</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Fecha límite</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($aprobadas as $a)
                        <tr>
                            <td>{{ $a->actividad }}</td>
                            <td>{{ $a->alumnoServicio->alumno->nombre ?? '—' }}</td>
                            <td>{{ $a->servicio->nombre ?? '—' }}</td>
                            <td><span class="badge badge-success">Aprobada</span></td>
                            <td>{{ $a->fecha_limite }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
