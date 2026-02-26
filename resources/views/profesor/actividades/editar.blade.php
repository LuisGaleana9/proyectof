@extends('profesor.layout')

@section('content')
    <div class="card">
        <h1 style="margin-top: 0;">Editar actividad</h1>
        <p class="muted">
            Servicio: {{ $actividad->servicio->nombre ?? '—' }}
        </p>

        <form action="{{ route('profesor.actividades.update', $actividad->id_actividad) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1rem;">
                <label for="id_alumno_servicio">Alumno</label>
                <select name="id_alumno_servicio" id="id_alumno_servicio" required>
                    <option value="">Selecciona un alumno</option>
                    @foreach($alumnosServicio as $as)
                        <option value="{{ $as->id }}" {{ old('id_alumno_servicio', $actividad->id_alumno_servicio) == $as->id ? 'selected' : '' }}>
                            {{ $as->alumno->nombre ?? '' }} {{ $as->alumno->apellidos_p ?? '' }} ({{ $as->tipo_servicio }})
                        </option>
                    @endforeach
                </select>
                @error('id_alumno_servicio')
                    <div class="muted" style="color:#b91c1c;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="actividad">Título de la actividad</label>
                <input type="text" id="actividad" name="actividad" value="{{ old('actividad', $actividad->actividad) }}" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="comentarios">Comentarios</label>
                <textarea id="comentarios" name="comentarios" rows="3">{{ old('comentarios', $actividad->comentarios) }}</textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="fecha_limite">Fecha límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" value="{{ old('fecha_limite', $actividad->fecha_limite) }}" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('profesor.actividades.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
