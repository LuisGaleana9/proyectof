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

        <label>Alumno:</label>
        <select name="id_alumno" required>
            <option value="">Seleccione un alumno</option>
            @foreach($alumnos as $alumno)
                <option value="{{ $alumno->id_usuario }}" {{ $servicio->id_alumno == $alumno->id_usuario ? 'selected' : '' }}>
                    {{ $alumno->nombre }} {{ $alumno->apellidos_p }} ({{ $alumno->matricula }})
                </option>
            @endforeach
        </select>

        <label>Dependencia:</label>
        <select name="id_dependencia" required>
            <option value="">Seleccione una dependencia</option>
            @foreach($dependencias as $dep)
                <option value="{{ $dep->id_dependencia }}" {{ $servicio->id_dependencia == $dep->id_dependencia ? 'selected' : '' }}>
                    {{ $dep->nombre }}
                </option>
            @endforeach
        </select>

        <label>Tipo de Servicio:</label>
        <select name="tipo_servicio" required>
            <option value="Regular" {{ $servicio->tipo_servicio == 'Regular' ? 'selected' : '' }}>Regular</option>
            <option value="Adelantando" {{ $servicio->tipo_servicio == 'Adelantando' ? 'selected' : '' }}>Adelantando</option>
        </select>

        <label>Estado:</label>
        <select name="estado_servicio" required>
            <option value="Activo" {{ $servicio->estado_servicio == 'Activo' ? 'selected' : '' }}>Activo</option>
            <option value="En pausa" {{ $servicio->estado_servicio == 'En pausa' ? 'selected' : '' }}>En pausa</option>
            <option value="Finalizado" {{ $servicio->estado_servicio == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
        </select>

        <label>Fecha de Inicio:</label>
        <input type="date" name="fecha_inicio" value="{{ $servicio->fecha_inicio }}" required>

        <label>Fecha de Fin:</label>
        <input type="date" name="fecha_fin" value="{{ $servicio->fecha_fin }}">

        <br>
        <button type="submit" class="btn">Actualizar</button>
    </form>
@endsection
