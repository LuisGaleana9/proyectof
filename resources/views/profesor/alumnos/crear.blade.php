@extends('profesor.layout')

@section('content')
    <h1>Crear Alumno</h1>

    <form action="{{ route('alumnos.store') }}" method="POST">
        @csrf

        <label>Nombre:</label>
        <input type="text" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required>

        <label>Apellido Paterno:</label>
        <input type="text" name="apellidos_p" placeholder="Apellido Paterno" value="{{ old('apellidos_p') }}" required>

        <label>Apellido Materno:</label>
        <input type="text" name="apellidos_m" placeholder="Apellido Materno" value="{{ old('apellidos_m') }}">

        <label>Matrícula:</label>
        <input type="text" name="matricula" id="matricula" placeholder="Matrícula" value="{{ old('matricula') }}" required>

        <label>Email:</label>
        <input type="text" id="email_preview" value="{{ old('matricula') ? strtolower(old('matricula')) . '@umich.mx' : '' }}" placeholder="matricula@umich.mx" readonly>

        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="Contraseña" required>

        <br>
        <button type="submit" class="btn">Guardar</button>
    </form>

    <script>
        (function () {
            const matricula = document.getElementById('matricula');
            const preview = document.getElementById('email_preview');
            if (!matricula || !preview) return;

            const sync = () => {
                const value = (matricula.value || '').trim().toLowerCase();
                preview.value = value ? `${value}@umich.mx` : '';
            };

            matricula.addEventListener('input', sync);
            sync();
        })();
    </script>
@endsection