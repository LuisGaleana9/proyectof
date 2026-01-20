@extends('profesor.layout')

@section('content')
    <div class="card">
        <h1 style="margin-top: 0;">Editar actividad</h1>
        <p class="muted">Alumno: {{ $actividad->servicio->alumno->nombre ?? '—' }}</p>

        <form action="{{ route('profesor.actividades.update', $actividad->id_actividad) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1rem;">
                <label for="actividad">Título de la actividad</label>
                <input type="text" id="actividad" name="actividad" value="{{ $actividad->actividad }}" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="comentarios">Comentarios</label>
                <textarea id="comentarios" name="comentarios" rows="3">{{ $actividad->comentarios }}</textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="fecha_limite">Fecha límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" value="{{ $actividad->fecha_limite }}" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('profesor.actividades.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
