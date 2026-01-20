@extends('alumno.layout')

@section('content')
<div class="card">
    <div class="actions" style="justify-content: space-between;">
        <div>
            <h2 style="margin: 0;">Reporte institucional</h2>
            <p class="muted" style="margin: 0.25rem 0 0;">Actividades aprobadas · Alumno: {{ $user->nombre }}</p>
        </div>
        <button type="button" class="btn btn-secondary" disabled>Generar reporte</button>
    </div>

    <div style="margin-top: 1rem;">
        <h3 style="margin-top: 1.5rem;">Listado de actividades aprobadas</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Total horas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $d)
                    <tr>
                        <td>{{ $d['titulo'] }}</td>
                        <td>{{ $d['fecha'] }}</td>
                        <td>
                            <span class="badge {{ $d['tipo'] === 'Adelantando' ? 'badge-info' : 'badge-success' }}">
                                {{ $d['tipo'] }}
                            </span>
                        </td>
                        <td>{{ $d['tipo'] === 'Adelantando' ? ($d['total_horas'] ?? '—') : 'No aplica' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No hay actividades aprobadas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
