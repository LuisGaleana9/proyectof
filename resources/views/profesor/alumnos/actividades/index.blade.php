@extends('profesor.layout')

@section('content')
    <h1>Actividades del Alumno</h1>

    <a href="{{ route('profesor.alumnos.actividades.crear', $alumnoId) }}" class="btn">Asignar nueva actividad</a>

    <table>
        <thead>
        <tr>
            <th>Título</th>
            <th>Servicio</th>
            <th>Fecha límite</th>
            <th>Estado</th>
            <th>Progreso</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividades as $act)
            @php
                $abierta = $act->horas->firstWhere('hora_final', null);
            @endphp
            <tr>
                <td>{{ $act->actividad }}</td>
                <td>{{ $act->servicio->dependencia->nombre ?? 'Servicio' }} ({{ $act->servicio->tipo_servicio }})</td>
                <td>{{ $act->fecha_limite }}</td>
                <td>
                    @if($act->estado === 'Activa') Pendiente @elseif($act->estado === 'Aprobada') Confirmada @else Denegada @endif
                </td>
                <td>
                    @if($abierta)
                        En progreso desde {{ \Carbon\Carbon::parse($abierta->hora_inicio)->format('H:i d/m') }}
                    @else
                        @if($act->servicio->tipo_servicio === 'Adelantando')
                            @php
                                $totalSegundos = $act->horas->reduce(function($carry, $h) {
                                    if ($h->hora_final) {
                                        return $carry + (strtotime($h->hora_final) - strtotime($h->hora_inicio));
                                    }
                                    return $carry;
                                }, 0);
                                $horasTotales = floor($totalSegundos / 3600);
                                $minTotales = floor(($totalSegundos % 3600) / 60);
                            @endphp
                            Total: {{ $horasTotales }}h {{ $minTotales }}m
                        @else
                            -
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection