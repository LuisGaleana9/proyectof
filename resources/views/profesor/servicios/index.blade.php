@extends('profesor.layout')

@section('content')
    <h1>Gestionar Servicios</h1>

    <a href="{{ route('servicios.create') }}" class="btn" style="margin-bottom: 1rem;">Crear servicio</a>

    @if($servicios->isEmpty())
        <p>No hay servicios registrados.</p>
    @else
        <ul>
            @foreach($servicios as $servicio)
                <li>
                    <span>
                        <strong>{{ $servicio->nombre }}</strong><br>
                        @if($servicio->descripcion)
                            <small>{{ $servicio->descripcion }}</small><br>
                        @endif
                        <small>Alumnos inscritos: {{ $servicio->alumno_servicios_count }}</small>
                        <br>
                        <a href="{{ route('servicios.edit', $servicio->id_servicio) }}" class="btn btn-secondary"
                            style="font-size: 0.8em; padding: 0.2rem 0.5rem; margin-top: 0.5rem;">Editar</a>
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
@endsection