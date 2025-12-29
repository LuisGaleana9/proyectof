<!DOCTYPE html>
<html>

<head>
    <title>Iniciar Sesión</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f0f2f5;
        }

        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 0.8em;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <h2 style="text-align: center">Gestion del servicio social</h2>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf

            <label>Correo Institucional:</label>
            <input type="email" name="email" required autofocus>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Entrar</button>
        </form>
    </div>

</body>

</html>