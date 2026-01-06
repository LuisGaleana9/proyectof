@extends('admin.layout')

@section('content')
    <h1>Editar Profesor</h1>

    <form action="{{ route('profesores.update', $profesor) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ $profesor->nombre }}" required>

        <label>Apellido Paterno:</label>
        <input type="text" name="apellidos_p" value="{{ $profesor->apellidos_p }}" required>

        <label>Apellido Materno:</label>
        <input type="text" name="apellidos_m" value="{{ $profesor->apellidos_m }}">

        <label>Matrícula:</label>
        <input type="text" name="matricula" value="{{ $profesor->matricula }}" required>

        <label>Email:</label>
        <input type="email" name="email" value="{{ $profesor->email }}" required>

        <label>Contraseña (Dejar en blanco para mantener):</label>
        <input type="password" name="password" placeholder="Nueva contraseña">

        <br>
        <button type="submit">Actualizar</button>
    </form>
@endsection