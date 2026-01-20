<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Alumno</title>
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
                <div class="brand-subtitle">Sistema de Servicio Social — Portal del Alumno</div>
            </div>
        </header>

        <nav>
            <a href="{{ url('/usuario') }}">Inicio</a>
            <a href="{{ route('alumno.reporte') }}">Reporte</a>

            <form action="{{ url('/logout') }}" method="POST" style="margin-left: auto;">
                @csrf
                <button type="submit" class="btn btn-danger" style="margin: 0; padding: 0.5rem 1rem;">Cerrar
                    Sesión</button>
            </form>
        </nav>

        <div class="content-wrapper">
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li style="list-style: disc;">{{ $error }}</li>
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