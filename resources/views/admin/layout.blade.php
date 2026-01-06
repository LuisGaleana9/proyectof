<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <div class="container">

        <header style="margin-bottom: 2rem;">
            <h1>Panel Admin</h1>
        </header>

        <nav>
            <a href="{{ route('profesores.index') }}">Profesores</a>
            <a href="{{ route('dependencias.index') }}">Dependencias</a>
        </nav>

        <div class="content-wrapper">
            @if ($errors->any())
                <div style="background-color: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                    <ul style="list-style: disc; padding-left: 1.5rem; margin: 0; background-color: transparent; box-shadow: none;">
                        @foreach ($errors->all() as $error)
                            <li style="border: none; padding: 0.25rem 0; display: list-item;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>

    </div>

</body>

</html>