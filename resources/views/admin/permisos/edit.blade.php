@extends('layouts.admin')

@section('title', 'Editar Permiso')
@section('page_title', 'Editar permiso')

@section('content')
<div class="contenedor-form">
    <div class="card">
        <form method="POST" action="{{ route('admin.permisos.update', $permiso) }}" class="formulario">
            @csrf
            @method('PUT')
            <div class="campo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="{{ $permiso->nombre }}" required>
            </div>
            <div class="campo">
                <label>Descripción</label>
                <textarea name="descripcion" required>{{ $permiso->descripcion }}</textarea>
            </div>
            <div class="acciones">
                <button type="submit" class="btn guardar">Actualizar</button>
                <a href="{{ route('admin.permisos.index') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
