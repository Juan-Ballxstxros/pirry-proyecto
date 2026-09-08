@extends('layouts.admin')

@section('title', 'Agregar Permiso')
@section('page_title', 'Agregar permiso')

@section('content')
<div class="contenedor-form">
    <div class="card">
        <form method="POST" action="{{ route('admin.permisos.store') }}" class="formulario">
            @csrf
            <div class="campo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required>
            </div>
            <div class="campo">
                <label>Descripción</label>
                <textarea name="descripcion" required>{{ old('descripcion') }}</textarea>
            </div>
            <div class="acciones">
                <button type="submit" class="btn guardar">Guardar</button>
                <a href="{{ route('admin.permisos.index') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
