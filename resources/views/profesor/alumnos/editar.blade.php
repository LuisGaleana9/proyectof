@extends('profesor.layout')

@section('content')
    <h1>Editar Alumno</h1>

    <form action="{{ route('alumnos.update', $alumno->id_usuario) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ $alumno->nombre }}" required>

        <label>Apellido Paterno:</label>
        <input type="text" name="apellidos_p" value="{{ $alumno->apellidos_p }}" required>

        <label>Apellido Materno:</label>
        <input type="text" name="apellidos_m" value="{{ $alumno->apellidos_m }}">

        <label>Matrícula:</label>
        <input type="text" name="matricula" value="{{ $alumno->matricula }}" required>

        <label>Email:</label>
        <input type="email" name="email" value="{{ $alumno->email }}" required>

        <label>Contraseña (Dejar en blanco para mantener):</label>
        <input type="password" name="password" placeholder="Nueva contraseña">

        <br>
        <button type="submit" class="btn">Actualizar</button>
    </form>
@endsection