@extends('profesor.layout')

@section('content')
    <h1>Editar Servicio</h1>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <p>Edite los datos del servicio o elimínelo si es necesario.</p>
        
        <form action="{{ route('servicios.destroy', $servicio->id_servicio) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este servicio?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Eliminar Servicio</button>
        </form>
    </div>

    <form action="{{ route('servicios.update', $servicio->id_servicio) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre del servicio:</label>
        <input type="text" name="nombre" value="{{ old('nombre', $servicio->nombre) }}" required>
        @error('nombre')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Descripción:</label>
        <textarea name="descripcion" rows="3">{{ old('descripcion', $servicio->descripcion) }}</textarea>
        @error('descripcion')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <br>
        <button type="submit" class="btn">Actualizar</button>
    </form>

    @if($alumnosInscritos->isNotEmpty())
        <div style="margin-top: 2rem;">
            <h3>Alumnos inscritos en este servicio</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Tipo</th>
                        <th>Inicio</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alumnosInscritos as $as)
                        <tr>
                            <td>{{ $as->alumno->nombre }} {{ $as->alumno->apellidos_p }} ({{ $as->alumno->matricula }})</td>
                            <td>
                                <span class="badge {{ $as->tipo_servicio === 'Adelantando' ? 'badge-info' : 'badge-success' }}">
                                    {{ $as->tipo_servicio }}
                                </span>
                            </td>
                            <td>{{ $as->fecha_inicio }}</td>
                            <td>
                                <span class="badge {{ $as->estado_servicio === 'Activo' ? 'badge-success' : ($as->estado_servicio === 'En pausa' ? 'badge-warning' : 'badge-info') }}">
                                    {{ $as->estado_servicio }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
