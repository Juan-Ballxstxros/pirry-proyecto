<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear Cuenta - El Pirry</title>
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
</head>
<body>

<div class="contenedor">
    <div class="left">
        <div class="texto">
            <h1>Únete a El Pirry</h1>
            <p>Crea tu cuenta y disfruta de nuestro menú</p>
        </div>
    </div>

    <div class="right">
        <form action="{{ route('register.post') }}" method="POST" class="login-box register-box">
            @csrf
            <h2>Crear Cuenta</h2>

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="input-box">
                <label>Nombre completo</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required>
            </div>

            <div class="input-box">
                <label>Documento</label>
                <input type="text" name="documento" value="{{ old('documento') }}" required>
            </div>

            <div class="input-box">
                <label>Correo electrónico</label>
                <input type="email" name="correo" value="{{ old('correo') }}" required>
            </div>

            <div class="input-box">
                <label>Usuario</label>
                <input type="text" name="usuario" value="{{ old('usuario') }}" required>
            </div>

            <div class="input-box">
                <label>Contraseña</label>
                <input type="password" name="contrasena" required minlength="6">
            </div>

            <div class="input-box">
                <label>Confirmar contraseña</label>
                <input type="password" name="contrasena_confirmation" required minlength="6">
            </div>

            <div class="botones">
                <button type="submit" class="btn ingresar">Registrarse</button>
                <a href="{{ route('login') }}" class="btn volver">Ya tengo cuenta</a>
                <a href="{{ route('home') }}" class="btn volver">Volver al inicio</a>
            </div>
        </form>
    </div>
</div>

<script>
window.flashMessages = window.flashMessages || [];
@if(session('success'))
  window.flashMessages.push({ type: 'success', message: @json(session('success')) });
@endif
@if(session('error'))
  window.flashMessages.push({ type: 'error', message: @json(session('error')) });
@endif
</script>
<script src="{{ asset('js/toast.js') }}"></script>

</body>
</html>
