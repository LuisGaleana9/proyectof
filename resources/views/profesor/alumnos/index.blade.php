@extends('profesor.layout')

@section('content')
    <h1>Alumnos</h1>

    <a href="{{ route('alumnos.create') }}" class="btn" style="margin-bottom: 1rem;">Crear alumno</a>

    <ul>
        @foreach($alumnos as $alumno)
            <li>
                <span>{{ $alumno->nombre }} {{ $alumno->apellidos_p }} - {{ $alumno->email }} ({{ $alumno->matricula }})</span>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('alumnos.edit', $alumno->id_usuario) }}" class="btn btn-secondary">Editar</a>

                    <form action="{{ route('alumnos.destroy', $alumno->id_usuario) }}" method="POST" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endsection