@extends('admin.layout')

@section('content')
    <h1>Crear Profesor</h1>

    <form action="{{ route('profesores.store') }}" method="POST">
        @csrf

        <label>Nombre:</label>
        <input type="text" name="nombre" placeholder="Nombre" required>

        <label>Apellido Paterno:</label>
        <input type="text" name="apellidos_p" placeholder="Apellido Paterno" required>

        <label>Apellido Materno:</label>
        <input type="text" name="apellidos_m" placeholder="Apellido Materno">

        <label>Matrícula:</label>
        <input type="text" name="matricula" placeholder="Matrícula" required>

        <label>Email:</label>
        <input type="email" name="email" placeholder="Email" required>

        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="Contraseña" required>

        <br>
        <button type="submit">Guardar</button>
    </form>
@endsection