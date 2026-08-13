<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión - El Pirry</title>
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="contenedor">
    <div class="left">
        <div class="texto">
            <h1>Bienvenido</h1>
            <p>Accede al sistema de gestión</p>
        </div>
    </div>

    <div class="right">
        <form action="{{ route('login.post') }}" method="POST" class="login-box">
            @csrf
            <h2>Iniciar Sesión</h2>

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="input-box">
                <label>Correo</label>
                <input type="email" name="correo" value="{{ old('correo') }}" required>
            </div>

            <div class="input-box">
                <label>Contraseña</label>
                <input type="password" name="contrasena" required>
            </div>

            <div class="botones">
                <button type="submit" class="btn ingresar">Ingresar</button>
                <a href="{{ route('home') }}" class="btn volver">Volver</a>
                <p class="register-link">
                    ¿No tienes una cuenta? <a href="{{ route('register') }}">Crear cuenta</a>
                </p>
                <p class="info-login">Si olvidó su contraseña, contacte al administrador del sistema.</p>
            </div>
        </form>
    </div>
</div>

</body>
</html>
