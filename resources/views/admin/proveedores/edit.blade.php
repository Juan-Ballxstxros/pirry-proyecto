@extends('layouts.admin')

@section('title', 'Editar Proveedor')
@section('page_title', 'Editar proveedor')

@section('content')
<div class="contenedor-form">
    <div class="card">
        <form action="{{ route('admin.proveedores.update', $proveedor) }}" method="POST" class="formulario">
            @csrf
            @method('PUT')
            <div class="grid">
                <div class="campo">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required>
                </div>
                <div class="campo">
                    <label>Identificación</label>
                    <input type="text" value="{{ $proveedor->identificacion }}" disabled>
                </div>
                <div class="campo">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}">
                </div>
                <div class="campo">
                    <label>Correo</label>
                    <input type="email" name="email" value="{{ old('email', $proveedor->email) }}" required>
                </div>
                <div class="campo" style="grid-column: span 2;">
                    <label>Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}">
                </div>
            </div>
            <div class="acciones">
                <button type="submit" class="btn guardar">Actualizar</button>
                <a href="{{ route('admin.proveedores.index') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
