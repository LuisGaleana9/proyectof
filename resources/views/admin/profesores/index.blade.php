<h1>Profesores</h1>

<a href="{{ route('profesores.create') }}">Crear profesor</a>

<ul>
    @foreach($profesores as $profesor)

        <li>
            {{ $profesor->nombre }} - {{ $profesor->email }}

            <a href="{{ route('profesores.edit', $profesor) }}">Editar</a>

            <form action="{{ route('profesores.destroy', $profesor) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        </li>
    @endforeach
</ul>