@extends('profesor.layout')

@section('content')
    <h1>Actividades Pendientes de Revisión</h1>

    <table>
        <thead>
        <tr>
            <th>Título</th>
            <th>Alumno</th>
            <th>Fecha límite</th>
            <th>Comentarios</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividades as $act)
            <tr>
                <td>{{ $act->actividad }}</td>
                <td>{{ $act->servicio->alumno->nombre ?? 'Alumno' }}</td>
                <td>{{ $act->fecha_limite }}</td>
                <td>{{ $act->comentarios }}</td>
                <td>
                    @php
                        $abierta = $act->horas->firstWhere('hora_final', null);
                    @endphp
                    @if(!$abierta)
                        <form action="{{ route('profesor.actividades.aprobar', $act->id_actividad) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn">Aprobar</button>
                        </form>
                    @endif
                    <form action="{{ route('profesor.actividades.rechazar', $act->id_actividad) }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn">Rechazar</button>
                    </form>
                    <form action="{{ route('profesor.actividades.regresar', $act->id_actividad) }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn">Regresar a en progreso</button>
                    </form>
                    @if(!$act->horas->count())
                        <form action="{{ route('profesor.actividades.cancelar', $act->id_actividad) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Cancelar</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection