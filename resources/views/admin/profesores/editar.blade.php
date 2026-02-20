@extends('admin.layout')

@section('content')
    <h1>Editar Profesor</h1>

    <form action="{{ route('profesores.update', $profesor) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ old('nombre', $profesor->nombre) }}" required>
        @error('nombre')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Apellido Paterno:</label>
        <input type="text" name="apellidos_p" value="{{ old('apellidos_p', $profesor->apellidos_p) }}" required>
        @error('apellidos_p')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Apellido Materno:</label>
        <input type="text" name="apellidos_m" value="{{ old('apellidos_m', $profesor->apellidos_m) }}">
        @error('apellidos_m')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Matrícula:</label>
        <input type="text" name="matricula" value="{{ old('matricula', $profesor->matricula) }}" required>
        @error('matricula')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email', $profesor->email) }}" required>
        @error('email')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Contraseña (Dejar en blanco para mantener):</label>
        <input type="password" name="password" placeholder="Nueva contraseña">
        @error('password')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <br>
        <button type="submit">Actualizar</button>
    </form>
@endsection