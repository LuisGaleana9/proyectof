<h1>Editar Profesor</h1>

<form action="{{ route('profesores.update', $profesor) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="nombre" value="{{ $profesor->nombre }}">
    <input type="text" name="apellidos_p" value="{{ $profesor->apellidos_p }}">
    <input type="text" name="apellidos_m" value="{{ $profesor->apellidos_m }}">
    <input type="text" name="matricula" value="{{ $profesor->matricula }}">
    <input type="email" name="email" value="{{ $profesor->email }}">
    <input type="password" name="password" value="{{ $profesor->password }}">

    <button type="submit">Actualizar</button>
</form>