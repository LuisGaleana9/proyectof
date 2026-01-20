<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Profesor</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <div class="container">

        <header style="margin-bottom: 2rem;">
            <div class="brand">
                <div class="brand-title">Facultad de Ingeniería Eléctrica · UMICH</div>
                <div class="brand-subtitle">Sistema de Servicio Social — Panel de Profesor</div>
            </div>
        </header>

        <nav>
            <a href="{{ url('/profesor') }}">Inicio</a>
            <a href="{{ route('alumnos.index') }}">Alumnos</a>
            <a href="{{ route('servicios.index') }}">Servicios</a>
            <a href="{{ route('profesor.actividades.index') }}">Actividades</a>
            <a href="{{ route('profesor.revisiones') }}">Revisiones</a>

            <form action="{{ url('/logout') }}" method="POST" style="margin-left: auto;">
                @csrf
                <button type="submit" class="btn btn-danger" style="margin: 0; padding: 0.5rem 1rem;">Cerrar Sesión</button>
            </form>
        </nav>

        <div class="content-wrapper">
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div
                    style="background-color: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                    <ul
                        style="list-style: disc; padding-left: 1.5rem; margin: 0; background-color: transparent; box-shadow: none;">
                        @foreach ($errors->all() as $error)
                            <li style="border: none; padding: 0.25rem 0; display: list-item;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>

        <div class="footer">Universidad Michoacana de San Nicolás de Hidalgo · Facultad de Ingeniería Eléctrica</div>

    </div>

</body>

</html>