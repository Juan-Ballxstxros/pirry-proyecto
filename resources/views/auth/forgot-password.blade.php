@extends('layouts.auth')

@section('title', 'Recuperar contraseña - El Pirry')

@section('side_title')
    <h1 class="display-6">¿Olvidaste tu contraseña?</h1>
@endsection
@section('side_text')
    <p class="fs-5 opacity-75">Tranquilo, te enviaremos un enlace a tu correo para crear una nueva.</p>
@endsection

@section('content')
<h2 class="fw-bold mb-4">Recuperar contraseña</h2>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('password.email') }}" method="POST">
    @csrf
    <div class="mb-4">
        <label class="form-label">Correo registrado</label>
        <input type="email" name="correo" class="form-control form-control-lg" value="{{ old('correo') }}" required autofocus>
    </div>

    <button type="submit" class="btn btn-pirry btn-lg w-100 mb-3">Enviar enlace de recuperación</button>
    <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">Volver a iniciar sesión</a>
</form>
@endsection
