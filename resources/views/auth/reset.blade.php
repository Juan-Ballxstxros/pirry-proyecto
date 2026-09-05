<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Restablecer Contraseña - El Pirry</title>
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
</head>
<body>

<div class="contenedor">
    <div class="left">
        <div class="texto">
            <h1>Nueva Contraseña</h1>
            <p>Crea una contraseña nueva para tu cuenta</p>
        </div>
    </div>

    <div class="right">
        <form action="{{ route('password.update') }}" method="POST" class="login-box register-box">
            @csrf
            <h2>Restablecer Contraseña</h2>

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="input-box">
                <label>Correo electrónico</label>
                <input type="email" name="correo" value="{{ old('correo', $correo) }}" required>
            </div>

            <div class="input-box">
                <label>Nueva contraseña</label>
                <input type="password" name="contrasena" required minlength="6">
            </div>

            <div class="input-box">
                <label>Confirmar contraseña</label>
                <input type="password" name="contrasena_confirmation" required minlength="6">
            </div>

            <div class="botones">
                <button type="submit" class="btn ingresar">Guardar contraseña</button>
                <a href="{{ route('login') }}" class="btn volver">Volver al login</a>
                <p class="register-link">Mínimo 6 caracteres, igual que al crear tu cuenta.</p>
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
