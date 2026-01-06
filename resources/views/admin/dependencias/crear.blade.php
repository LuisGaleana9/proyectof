@extends('admin.layout')

@section('content')

    <h1>Crear Dependencia</h1>

    <form action="{{ route('dependencias.store') }}" method="POST">
        @csrf

        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        <br><br>

        <label>Profesor Responsable:</label>
        <select name="id_profesor_responsable" required>
            <option value="">Seleccione un profesor</option>
            @foreach($profesores as $profe)
                <option value="{{ $profe->id_usuario }}">
                    {{ $profe->nombre }} {{ $profe->apellidos_p }} {{ $profe->apellidos_m }}
                </option>
            @endforeach
        </select>
        <br><br>

        <button type="submit">Guardar</button>
    </form>

@endsection