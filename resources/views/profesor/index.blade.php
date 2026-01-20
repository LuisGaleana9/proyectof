@extends('profesor.layout')

@section('content')
    <div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
        <div>
            <h2 style="margin: 0;">Panel Académico</h2>
            <p class="muted" style="margin: 0.25rem 0 0;">Facultad de Ingeniería Eléctrica · UMICH</p>
        </div>
        <div class="actions">
            <a class="btn btn-secondary" href="{{ route('profesor.actividades.index') }}">Gestionar actividades</a>
            <a class="btn btn-primary" href="{{ route('profesor.revisiones') }}">Ver actividades en revisión</a>
        </div>
    </div>
@endsection