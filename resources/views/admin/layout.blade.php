<!DOCTYPE html>
<html>

<head>
    <title>Admin</title>
</head>

<body>

    <h1>Panel Admin</h1>

    <nav>
        <a href="{{ route('profesores.index') }}">Profesores</a>
    </nav>

    <hr>

    @yield('content')

</body>

</html>