<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Contraseña - El Pirry</title>
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
</head>
<body>

<div class="contenedor">
    <div class="left">
        <div class="texto">
            <h1>El Pirry</h1>
            <p>Te ayudamos a recuperar tu acceso</p>
        </div>
    </div>

    <div class="right">
        <form action="{{ route('password.email') }}" method="POST" class="login-box">
            @csrf
            <h2>Recuperar Contraseña</h2>

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="input-box">
                <label>Correo electrónico</label>
                <input type="email" name="correo" value="{{ old('correo') }}" required>
            </div>

            <div class="botones">
                <button type="submit" class="btn ingresar">Enviar enlace</button>
                <a href="{{ route('login') }}" class="btn volver">Volver al login</a>
                <p class="register-link">
                    Recibirás un enlace temporal para crear una nueva contraseña.
                </p>
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
