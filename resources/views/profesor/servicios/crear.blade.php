@extends('profesor.layout')

@section('content')
    <h1>Dar de alta Servicio</h1>

    <form action="{{ route('servicios.store') }}" method="POST">
        @csrf

        <label>Alumno:</label>
        <select name="id_alumno" required>
            <option value="">Seleccione un alumno</option>
            @foreach($alumnos as $alumno)
                <option value="{{ $alumno->id_usuario }}">
                    {{ $alumno->nombre }} {{ $alumno->apellidos_p }} ({{ $alumno->matricula }})
                </option>
            @endforeach
        </select>

        <label>Dependencia:</label>
        <select name="id_dependencia" required>
            <option value="">Seleccione una dependencia</option>
            @foreach($dependencias as $dep)
                <option value="{{ $dep->id_dependencia }}">{{ $dep->nombre }}</option>
            @endforeach
        </select>

        <label>Tipo de Servicio:</label>
        <select name="tipo_servicio" required>
            <option value="Regular">Regular</option>
            <option value="Adelantando">Adelantando</option>
        </select>

        <label>Fecha de Inicio:</label>
        <input type="date" name="fecha_inicio" value="{{ date('Y-m-d') }}" required>

        <br>
        <button type="submit" class="btn">Guardar</button>
    </form>
@endsection