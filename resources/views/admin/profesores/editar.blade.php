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

        <label>Dependencia:</label>
        <select name="id_dependencia" id="select_dependencia">
            <option value="">Sin dependencia</option>
            @foreach($dependencias as $dep)
                <option value="{{ $dep->id_dependencia }}" {{ old('id_dependencia', $profesor->id_dependencia) == $dep->id_dependencia ? 'selected' : '' }}>
                    {{ $dep->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_dependencia')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <div style="margin-top: 0.5rem;">
            <label>
                <input type="checkbox" id="toggle_nueva_dep" style="width: auto; display: inline;"> Crear nueva dependencia
            </label>
            <input type="text" name="nueva_dependencia" id="input_nueva_dep" placeholder="Nombre de la nueva dependencia" value="{{ old('nueva_dependencia') }}" style="display: none; margin-top: 0.5rem;">
        </div>
        @error('nueva_dependencia')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <br>
        <button type="submit">Actualizar</button>
    </form>

    <script>
        (function() {
            const toggle = document.getElementById('toggle_nueva_dep');
            const input = document.getElementById('input_nueva_dep');
            const select = document.getElementById('select_dependencia');
            if (!toggle || !input || !select) return;

            toggle.addEventListener('change', function() {
                if (this.checked) {
                    input.style.display = 'block';
                    select.disabled = true;
                } else {
                    input.style.display = 'none';
                    input.value = '';
                    select.disabled = false;
                }
            });
        })();
    </script>
@endsection