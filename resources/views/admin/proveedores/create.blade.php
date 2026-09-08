@extends('layouts.admin')

@section('title', 'Registrar Proveedor')
@section('page_title', 'Registrar proveedor')

@section('content')
<div class="contenedor-form">
    <div class="card">
        @if ($errors->any())
            <div class="pill pill-danger" style="display:block; margin-bottom:16px;">
                Revisa los campos: hay datos inválidos o repetidos.
            </div>
        @endif
        <form action="{{ route('admin.proveedores.store') }}" method="POST" class="formulario">
            @csrf
            <div class="grid">
                <div class="campo">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                </div>
                <div class="campo">
                    <label>Identificación (NIT/CC)</label>
                    <input type="text" name="identificacion" value="{{ old('identificacion') }}" required>
                </div>
                <div class="campo">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}">
                </div>
                <div class="campo">
                    <label>Correo</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="campo" style="grid-column: span 2;">
                    <label>Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}">
                </div>
            </div>
            <div class="acciones">
                <button type="submit" class="btn guardar">Registrar</button>
                <a href="{{ route('admin.proveedores.index') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
