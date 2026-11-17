@extends('layouts.auth')

@section('title', 'Iniciar Sesión - El Pirry')

@section('side_title')
    <h1 class="display-6">Bienvenido de nuevo</h1>
@endsection
@section('side_text')
    <p class="fs-5 opacity-75">Inicia sesión para pedir tus salchipapas y hamburguesas favoritas.</p>
@endsection

@section('content')
<h2 class="fw-bold mb-4">Iniciar sesión</h2>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form action="{{ route('login.post') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Correo</label>
        <input type="email" name="correo" class="form-control form-control-lg" value="{{ old('correo') }}" required autofocus>
    </div>
    <div class="mb-2">
        <label class="form-label">Contraseña</label>
        <input type="password" name="contrasena" class="form-control form-control-lg" required>
    </div>
    <div class="mb-4 text-end">
        <a href="{{ route('password.request') }}" class="link-secondary small">¿Olvidaste tu contraseña?</a>
    </div>

    <button type="submit" class="btn btn-pirry btn-lg w-100 mb-3">Ingresar</button>
    <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 mb-4">Volver a la tienda</a>

    <p class="text-center text-muted mb-0">
        ¿No tienes una cuenta? <a href="{{ route('register') }}">Crear cuenta</a>
    </p>
</form>
@endsection
