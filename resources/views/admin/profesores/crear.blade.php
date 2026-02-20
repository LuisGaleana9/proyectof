@extends('admin.layout')

@section('content')
    <h1>Crear Profesor</h1>

    <form action="{{ route('profesores.store') }}" method="POST">
        @csrf

        <label>Nombre:</label>
        <input type="text" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required>
        @error('nombre')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Apellido Paterno:</label>
        <input type="text" name="apellidos_p" placeholder="Apellido Paterno" value="{{ old('apellidos_p') }}" required>
        @error('apellidos_p')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Apellido Materno:</label>
        <input type="text" name="apellidos_m" placeholder="Apellido Materno" value="{{ old('apellidos_m') }}">
        @error('apellidos_m')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Numero de empleado:</label>
        <input type="text" name="matricula" placeholder="Numero de empleado" value="{{ old('matricula') }}" required>
        @error('matricula')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Email:</label>
        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
        @error('email')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="Contraseña" required>
        @error('password')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <br>
        <button type="submit">Guardar</button>
    </form>
@endsection