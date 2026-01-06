@extends('admin.layout')

@section('content')

    <h1>Dependencias</h1>

    <a href="{{ route('dependencias.create') }}" class="btn" style="margin-bottom: 1rem;">Crear dependencia</a>

    <ul>
        @foreach($dependencias as $dep)
            <li>
                <span>
                    <strong>{{ $dep->nombre }}</strong>
                    @if($dep->responsable)
                        <br>
                        <span style="font-size: 0.9em; color: #6b7280;">Responsable: {{ $dep->responsable->nombre }}
                            {{ $dep->responsable->apellidos_p }}</span>
                    @else
                        <br>
                        <span style="font-size: 0.9em; color: #9ca3af;">Sin responsable</span>
                    @endif
                </span>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('dependencias.edit', $dep) }}" class="btn btn-secondary">Editar</a>
                    <form action="{{ route('dependencias.destroy', $dep) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>

@endsection