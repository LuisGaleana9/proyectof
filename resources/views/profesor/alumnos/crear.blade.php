@extends('profesor.layout')

@section('content')
    <h1>Crear Alumno</h1>

    <form action="{{ route('alumnos.store') }}" method="POST">
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

        <label>Matrícula:</label>
        <input type="text" name="matricula" id="matricula" placeholder="Matrícula" value="{{ old('matricula') }}" required>
        @error('matricula')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Email:</label>
        <input type="text" id="email_preview" value="{{ old('matricula') ? strtolower(old('matricula')) . '@umich.mx' : '' }}" placeholder="matricula@umich.mx" readonly>

        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="Contraseña" required>
        @error('password')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <hr style="margin: 1.5rem 0;">
        <h3>Asignar a servicio</h3>

        <label>Servicio:</label>
        <select name="id_servicio" required>
            <option value="">Seleccione un servicio</option>
            @foreach($servicios as $servicio)
                <option value="{{ $servicio->id_servicio }}" {{ old('id_servicio') == $servicio->id_servicio ? 'selected' : '' }}>
                    {{ $servicio->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_servicio')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Tipo de Servicio:</label>
        <select name="tipo_servicio" required>
            <option value="Regular" {{ old('tipo_servicio') == 'Regular' ? 'selected' : '' }}>Regular</option>
            <option value="Adelantando" {{ old('tipo_servicio') == 'Adelantando' ? 'selected' : '' }}>Adelantando</option>
        </select>
        @error('tipo_servicio')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Fecha de Inicio:</label>
        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
        @error('fecha_inicio')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

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