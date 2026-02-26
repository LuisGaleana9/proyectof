@extends('profesor.layout')

@section('content')
    <h1>Crear Servicio</h1>

    <form action="{{ route('servicios.store') }}" method="POST">
        @csrf

        <label>Nombre del servicio:</label>
        <input type="text" name="nombre" placeholder="Ej: Servicio de Laboratorios" value="{{ old('nombre') }}" required>
        @error('nombre')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <label>Descripción:</label>
        <textarea name="descripcion" rows="3" placeholder="Describe brevemente el servicio...">{{ old('descripcion') }}</textarea>
        @error('descripcion')
            <div class="muted" style="color:#b91c1c; margin-top:-0.5rem; margin-bottom:0.75rem;">{{ $message }}</div>
        @enderror

        <br>
        <button type="submit" class="btn">Guardar</button>
    </form>
@endsection