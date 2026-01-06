@extends('admin.layout')

@section('content')

    <h1>Editar Dependencia</h1>

    <form action="{{ route('dependencias.update', $dependencia) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ $dependencia->nombre }}" required>
        <br><br>

        <label>Profesor Responsable:</label>
        <select name="id_profesor_responsable" required>
            <option value="">Seleccione un profesor</option>
            @foreach($profesores as $profe)
                <option value="{{ $profe->id_usuario }}" {{ $dependencia->id_profesor_responsable == $profe->id_usuario ? 'selected' : '' }}>
                    {{ $profe->nombre }} {{ $profe->apellidos_p }} {{ $profe->apellidos_m }}
                </option>
            @endforeach
        </select>
        <br><br>

        <button type="submit" class="btn">Actualizar</button>
    </form>

@endsection