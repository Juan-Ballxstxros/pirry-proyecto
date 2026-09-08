@extends('layouts.admin')

@section('title', 'Registrar Compra')
@section('page_title', 'Registrar compra de insumo')

@section('content')
<div class="contenedor-form">
    <div class="card">
        @if($ingredientes->isEmpty() || $proveedores->isEmpty())
            <p class="text-muted">
                Necesitas al menos un <a href="{{ route('admin.ingredientes.create') }}" class="link-cta">ingrediente</a>
                y un <a href="{{ route('admin.proveedores.create') }}" class="link-cta">proveedor</a> activos antes de registrar una compra.
            </p>
        @else
        <form action="{{ route('admin.compras.store') }}" method="POST" class="formulario">
            @csrf
            <div class="grid">
                <div class="campo">
                    <label>Ingrediente</label>
                    <select name="id_ingrediente" required>
                        @foreach($ingredientes as $ing)
                            <option value="{{ $ing->id }}">{{ $ing->nombre }} ({{ $ing->unidad_medida }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="campo">
                    <label>Proveedor</label>
                    <select name="id_proveedor" required>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="campo">
                    <label>Cantidad</label>
                    <input type="number" step="0.01" min="0.01" name="cantidad" required>
                </div>
                <div class="campo">
                    <label>Precio unitario</label>
                    <input type="number" step="0.01" min="0.01" name="precio_unitario" required>
                </div>
            </div>
            <div class="acciones">
                <button type="submit" class="btn guardar">Registrar compra</button>
                <a href="{{ route('admin.compras.index') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
