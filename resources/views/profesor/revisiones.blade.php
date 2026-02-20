@extends('profesor.layout')

@section('content')
<div class="card">
    <div class="actions" style="justify-content: space-between;">
        <div>
            <h2 style="margin: 0;">Actividades en revisión</h2>
            <p class="muted" style="margin: 0.25rem 0 0;">Aprueba las actividades terminadas por alumnos.</p>
        </div>
    </div>

    <div style="margin-top: 1rem;" class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Actividad</th>
                    <th>Alumno</th>
                    <th>Tipo</th>
                    <th>Fecha límite</th>
                    <th>Registros de horas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividades as $a)
                    <tr>
                        <td class="actividad-col">{{ $a->actividad }}</td>
                        <td>{{ $a->servicio->alumno->nombre ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $a->servicio->tipo_servicio === 'Adelantando' ? 'badge-info' : 'badge-success' }}">
                                {{ $a->servicio->tipo_servicio }}
                            </span>
                        </td>
                        <td>{{ $a->fecha_limite }}</td>
                        <td class="horas-col">
                            @if($a->servicio->tipo_servicio === 'Adelantando')
                                <div class="table-responsive">
                                <table class="table horas-table" style="margin: 0;">
                                    <thead>
                                        <tr>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                            <th>Total</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalMinutos = 0; @endphp
                                        @forelse($a->horas as $h)
                                            @php
                                                $minutosRegistro = 0;
                                                if ($h->hora_final) {
                                                    if (\Illuminate\Support\Facades\Schema::hasColumn('horas', 'horas_totales') && $h->horas_totales !== null) {
                                                        $minutosRegistro = (int) round(((float) $h->horas_totales) * 60);
                                                    } else {
                                                        $minutosRegistro = \Carbon\Carbon::parse($h->hora_inicio)->diffInMinutes(\Carbon\Carbon::parse($h->hora_final));
                                                    }
                                                }
                                                $totalMinutos += $minutosRegistro;
                                            @endphp
                                            <tr>
                                                <td>{{ $h->hora_inicio }}</td>
                                                <td>{{ $h->hora_final ?? 'Abierto' }}</td>
                                                <td>{{ intdiv($minutosRegistro, 60) }}h {{ $minutosRegistro % 60 }}m</td>
                                                <td>
                                                    <form method="POST" action="{{ route('profesor.revisiones.horas.rechazar', $h->id_horas) }}" onsubmit="return confirm('¿Seguro que deseas rechazar y eliminar este registro de horas?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Rechazar registro</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No hay registros de horas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                </div>
                                <div class="muted" style="margin-top: 0.5rem;">
                                    Total válido actual: {{ intdiv($totalMinutos, 60) }}h {{ $totalMinutos % 60 }}m
                                </div>
                            @else
                                <span class="muted">No aplica (servicio Regular).</span>
                            @endif
                        </td>
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
                    <tr><td colspan="6">No hay actividades en revisión.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
