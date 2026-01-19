@extends('profesor.layout')

@section('content')
    <h1>Asignar Actividad al Alumno</h1>

    @if($servicios->isEmpty())
        <p>No hay servicios activos para este alumno bajo tu asesoría.</p>
    @else
        <form action="{{ route('profesor.alumnos.actividades.guardar', $alumnoId) }}" method="POST">
            @csrf

            <label>Servicio:</label>
            <select name="id_servicio" required>
                <option value="">Seleccione servicio</option>
                @foreach($servicios as $serv)
                    <option value="{{ $serv->id_servicio }}">
                        {{ $serv->dependencia->nombre ?? 'Dependencia' }} ({{ $serv->tipo_servicio }})
                    </option>
                @endforeach
            </select>

            <label>Título de la actividad:</label>
            <input type="text" name="actividad" required>

            <label>Descripción / Comentarios:</label>
            <textarea name="comentarios" rows="4" placeholder="Detalles de la actividad"></textarea>

            <label>Fecha límite:</label>
            <input type="date" name="fecha_limite" required>

            <button type="submit" class="btn">Asignar</button>
        </form>
    @endif
@endsection