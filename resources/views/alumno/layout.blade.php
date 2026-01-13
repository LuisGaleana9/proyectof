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
            <h1>Panel Alumno</h1>
        </header>

        <nav>
            <a href="{{ url('/usuario') }}">Inicio</a>

            <form action="{{ url('/logout') }}" method="POST" style="margin-left: auto;">
                @csrf
                <button type="submit" class="btn btn-danger" style="margin: 0; padding: 0.5rem 1rem;">Cerrar
                    Sesión</button>
            </form>
        </nav>

        <div class="content-wrapper">
            @yield('content')
        </div>

    </div>

</body>

</html>