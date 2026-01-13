@extends('profesor.layout')

@section('content')
    <h1>Gestionar Servicios</h1>

    <a href="{{ route('servicios.create') }}" class="btn" style="margin-bottom: 1rem;">Dar de alta servicio</a>

    @if($servicios->isEmpty())
        <p>No hay servicios registrados.</p>
    @else
        <ul>
            @foreach($servicios as $servicio)
                <li>
                    <span>
                        <strong>Alumno:</strong> {{ $servicio->alumno->nombre }} {{ $servicio->alumno->apellidos_p }} <br>
                        <strong>Dependencia:</strong> {{ $servicio->dependencia->nombre }} <br>
                        <small>Inicio: {{ $servicio->fecha_inicio }} | Tipo: {{ $servicio->tipo_servicio }} | Estado:
                            {{ $servicio->estado_servicio }}</small>
                        <br>
                        <a href="{{ route('servicios.edit', $servicio->id_servicio) }}" class="btn btn-secondary"
                            style="font-size: 0.8em; padding: 0.2rem 0.5rem; margin-top: 0.5rem;">Editar</a>
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
@endsection