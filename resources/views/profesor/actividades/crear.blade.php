@extends('profesor.layout')

@section('content')
    <div class="card">
        <h1 style="margin-top: 0;">Asignar actividad</h1>
        <p class="muted">Selecciona un servicio y un alumno.</p>

        <form action="{{ route('profesor.actividades.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 1rem;">
                <label for="id_servicio">Servicio</label>
                <select name="id_servicio" id="id_servicio" required>
                    <option value="">Selecciona un servicio</option>
                    @foreach($servicios as $servicio)
                        <option value="{{ $servicio->id_servicio }}" {{ old('id_servicio') == $servicio->id_servicio ? 'selected' : '' }}>
                            {{ $servicio->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_servicio')
                    <div class="muted" style="color:#b91c1c;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="id_alumno_servicio">Alumno</label>
                <select name="id_alumno_servicio" id="id_alumno_servicio" required>
                    <option value="">Selecciona un alumno</option>
                </select>
                @error('id_alumno_servicio')
                    <div class="muted" style="color:#b91c1c;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="actividad">Título de la actividad</label>
                <input type="text" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                @error('actividad')
                    <div class="muted" style="color:#b91c1c;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="comentarios">Comentarios</label>
                <textarea id="comentarios" name="comentarios" rows="3">{{ old('comentarios') }}</textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="fecha_limite">Fecha límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" value="{{ old('fecha_limite') }}" required>
                @error('fecha_limite')
                    <div class="muted" style="color:#b91c1c;">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('profesor.actividades.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const alumnosPorServicio = @json($alumnosPorServicio);
            const servicioSelect = document.getElementById('id_servicio');
            const alumnoSelect = document.getElementById('id_alumno_servicio');

            servicioSelect.addEventListener('change', function () {
                const servicioId = this.value;
                alumnoSelect.innerHTML = '<option value="">Selecciona un alumno</option>';

                if (servicioId && alumnosPorServicio[servicioId]) {
                    alumnosPorServicio[servicioId].forEach(function (as) {
                        const opt = document.createElement('option');
                        opt.value = as.id;
                        opt.textContent = as.alumno.nombre + ' ' + (as.alumno.apellidos_p || '') + ' (' + as.tipo_servicio + ')';
                        alumnoSelect.appendChild(opt);
                    });
                }
            });

            if (servicioSelect.value) {
                servicioSelect.dispatchEvent(new Event('change'));
            }
        })();
    </script>
@endsection
