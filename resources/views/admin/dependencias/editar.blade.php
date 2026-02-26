@extends('admin.layout')

@section('content')

    <h1>Editar Dependencia</h1>

    <form action="{{ route('dependencias.update', $dependencia) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ old('nombre', $dependencia->nombre) }}" required>
        @error('nombre')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror
        <br><br>

        <button type="submit" class="btn">Actualizar</button>
    </form>

@endsection