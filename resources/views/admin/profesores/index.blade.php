@extends('admin.layout')

@section('content')
    <h1>Profesores</h1>

    <a href="{{ route('profesores.create') }}" class="btn" style="margin-bottom: 1rem;">Crear profesor</a>

    <ul>
        @foreach($profesores as $profesor)
            <li>
                <span>{{ $profesor->nombre }} {{ $profesor->apellidos_p }} - {{ $profesor->email }}</span>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('profesores.edit', $profesor) }}" class="btn btn-secondary">Editar</a>

                    <form action="{{ route('profesores.destroy', $profesor) }}" method="POST" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endsection