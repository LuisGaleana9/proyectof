@extends('alumno.layout')

@section('content')
    <h1>Informe de Actividades Completadas</h1>

    <ul>
        @forelse($actividades as $act)
            <li>{{ $act->actividad }}</li>
        @empty
            <li>No hay actividades aprobadas.</li>
        @endforelse
    </ul>

    <button onclick="window.print()" class="btn">Imprimir / Guardar PDF</button>
@endsection