@extends('profesor.layout')

@section('content')
    <div class="card">
        <h1 style="margin-top: 0;">Asignar actividad</h1>
        <p class="muted">Selecciona un alumno con servicio activo y define la tarea.</p>

        <form action="{{ route('profesor.actividades.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 1rem;">
                <label for="id_servicio">Alumno/Servicio</label>
                <select name="id_servicio" id="id_servicio" required>
                    <option value="">Selecciona un alumno</option>
                    @foreach($servicios as $servicio)
                        <option value="{{ $servicio->id_servicio }}">
                            {{ $servicio->alumno->nombre }} {{ $servicio->alumno->apellidos_p }} - {{ $servicio->tipo_servicio }}
                        </option>
                    @endforeach
                </select>
                <span class="muted">Solo servicios activos.</span>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="actividad">Título de la actividad</label>
                <input type="text" id="actividad" name="actividad" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="comentarios">Comentarios</label>
                <textarea id="comentarios" name="comentarios" rows="3"></textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="fecha_limite">Fecha límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('profesor.actividades.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
