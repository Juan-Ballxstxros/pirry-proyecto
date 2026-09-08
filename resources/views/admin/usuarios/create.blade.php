@extends('layouts.admin')

@section('title', 'Registrar Usuario')
@section('page_title', 'Registrar usuario')

@section('content')
<div class="header">
    <div></div>
    <a href="{{ route('admin.usuarios.index') }}" class="btn-volver">← Volver</a>
</div>

<div class="contenedor-form">
    <div class="card">
        @if ($errors->any())
            <div class="pill pill-danger" style="display:block; margin-bottom:16px;">
                Revisa los campos marcados: hay datos inválidos o repetidos.
            </div>
        @endif

        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="formulario">
            @csrf
            <div class="grid">
                <div class="campo">
                    <label>Nombres</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                </div>
                <div class="campo">
                    <label>Documento</label>
                    <input type="text" name="documento" value="{{ old('documento') }}" required>
                </div>
                <div class="campo">
                    <label>Correo</label>
                    <input type="email" name="correo" value="{{ old('correo') }}" required>
                </div>
                <div class="campo">
                    <label>Usuario</label>
                    <input type="text" name="usuario" value="{{ old('usuario') }}" required>
                </div>
                <div class="campo">
                    <label>Contraseña</label>
                    <input type="password" name="contrasena" required>
                </div>
                <div class="campo">
                    <label>Rol</label>
                    <select name="id_rol" required>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_roles }}">{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="campo">
                    <label>Estado</label>
                    <select name="estado" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="acciones">
                <button type="submit" class="btn guardar">Registrar</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
