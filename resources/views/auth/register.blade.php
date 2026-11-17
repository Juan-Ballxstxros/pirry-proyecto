@extends('layouts.auth')

@section('title', 'Crear Cuenta - El Pirry')

@section('side_title')
    <h1 class="display-6">Únete a El Pirry</h1>
@endsection
@section('side_text')
    <p class="fs-5 opacity-75">Crea tu cuenta y disfruta de nuestro menú completo.</p>
@endsection

@section('content')
<h2 class="fw-bold mb-4">Crear cuenta</h2>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form action="{{ route('register.post') }}" method="POST">
    @csrf
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Documento</label>
            <input type="text" name="documento" class="form-control" value="{{ old('documento') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" value="{{ old('usuario') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required minlength="6">
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="contrasena_confirmation" class="form-control" required minlength="6">
        </div>
    </div>

    <button type="submit" class="btn btn-pirry btn-lg w-100 mb-3">Registrarse</button>
    <div class="d-flex gap-2">
        <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">Ya tengo cuenta</a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100">Volver</a>
    </div>
</form>
@endsection
