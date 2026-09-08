@extends('layouts.auth')

@section('title', 'Nueva contraseña - El Pirry')

@section('side_title')
    <h1 class="display-6">Crea tu nueva contraseña</h1>
@endsection
@section('side_text')
    <p class="fs-5 opacity-75">Este enlace es de un solo uso y expira a los 15 minutos.</p>
@endsection

@section('content')
<h2 class="fw-bold mb-4">Nueva contraseña</h2>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3">
        <label class="form-label">Correo</label>
        <input type="email" name="correo" class="form-control form-control-lg" value="{{ old('correo', $correo) }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nueva contraseña</label>
        <input type="password" name="contrasena" class="form-control form-control-lg" required minlength="6">
    </div>
    <div class="mb-4">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password" name="contrasena_confirmation" class="form-control form-control-lg" required minlength="6">
    </div>

    <button type="submit" class="btn btn-pirry btn-lg w-100">Restablecer contraseña</button>
</form>
@endsection
