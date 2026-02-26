@extends('admin.layout')

@section('content')

    <h1>Crear Dependencia</h1>

    <form action="{{ route('dependencias.store') }}" method="POST">
        @csrf

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}" required>
        @error('nombre')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror
        <br><br>

        <button type="submit">Guardar</button>
    </form>

@endsection