@extends('layouts.admin')

@section('title', 'Nuevo Ingrediente')
@section('page_title', 'Nuevo ingrediente')

@section('content')
<div class="contenedor-form">
    <div class="card">
        <form action="{{ route('admin.ingredientes.store') }}" method="POST" class="formulario">
            @csrf
            <div class="grid">
                <div class="campo">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                </div>
                <div class="campo">
                    <label>Unidad de medida</label>
                    <select name="unidad_medida" required>
                        <option value="unidad">Unidad</option>
                        <option value="g">Gramos (g)</option>
                        <option value="kg">Kilogramos (kg)</option>
                        <option value="ml">Mililitros (ml)</option>
                        <option value="l">Litros (l)</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Stock mínimo (alerta)</label>
                    <input type="number" step="0.01" min="0" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" required>
                </div>
            </div>
            <div class="acciones">
                <button type="submit" class="btn guardar">Registrar</button>
                <a href="{{ route('admin.ingredientes.index') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
