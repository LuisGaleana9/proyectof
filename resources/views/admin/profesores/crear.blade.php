<h1>Crear Profesor</h1>

<form action="{{ route('profesores.store') }}" method="POST">
    @csrf

    <input type="text" name="nombre" placeholder="Nombre">
    <input type="text" name="apellidos_p" placeholder="Apellido Paterno">
    <input type="text" name="apellidos_m" placeholder="Apellido Materno">
    <input type="text" name="matricula" placeholder="Matrícula">
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Contraseña">

    <button type="submit">Guardar</button>
</form>