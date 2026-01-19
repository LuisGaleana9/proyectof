@extends('alumno.layout')

@section('content')
    <h1>Mis Actividades</h1>

    <a href="{{ route('alumno.actividades.informe') }}" class="btn">Generar informe de completadas</a>

    <table>
        <thead>
        <tr>
            <th>Título</th>
            <th>Estado</th>
            <th>Tipo Servicio</th>
            <th>Tiempo</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividades as $act)
            <tr>
                <td>{{ $act->actividad }}</td>
                <td>
                    @if($act->estado === 'Activa') Pendiente @elseif($act->estado === 'Aprobada') Confirmada @else Denegada @endif
                </td>
                <td>{{ $act->servicio->tipo_servicio }}</td>
                <td>
                    @php
                        $abierta = $act->horas->firstWhere('hora_final', null);
                        $totalSegundos = $act->horas->reduce(function($carry, $h) {
                            if ($h->hora_final) {
                                return $carry + (strtotime($h->hora_final) - strtotime($h->hora_inicio));
                            }
                            return $carry;
                        }, 0);
                        $horasTotales = floor($totalSegundos / 3600);
                        $minTotales = floor(($totalSegundos % 3600) / 60);
                    @endphp
                    @if($act->servicio->tipo_servicio === 'Adelantando')
                        @if($abierta)
                            En curso desde {{ \Carbon\Carbon::parse($abierta->hora_inicio)->format('H:i') }}
                        @else
                            Total: {{ $horasTotales }}h {{ $minTotales }}m
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($act->servicio->tipo_servicio === 'Adelantando')
                        @if($abierta)
                            <form action="{{ route('alumno.actividades.stop', $act->id_actividad) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn">Terminar jornada</button>
                            </form>
                        @else
                            <form action="{{ route('alumno.actividades.start', $act->id_actividad) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn">Iniciar jornada</button>
                            </form>
                        @endif
                    @endif

                    @php $tieneHoras = $act->horas->count() > 0; @endphp
                    @if($act->estado === 'Activa' && (!$tieneHoras || ($act->servicio->tipo_servicio === 'Adelantando' && $abierta)))
                        <form action="{{ route('alumno.actividades.completar', $act->id_actividad) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn">Marcar como completada</button>
                        </form>
                    @elseif($act->estado === 'Activa' && $tieneHoras && !$abierta)
                        Completada (pendiente de confirmación)
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection