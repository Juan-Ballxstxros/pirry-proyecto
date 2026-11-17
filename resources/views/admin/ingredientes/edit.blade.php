@extends('layouts.admin')

@section('title', 'Gestionar Ingrediente')
@section('page_title', $ingrediente->nombre)
@section('page_subtitle', 'Datos, proveedores y productos que lo usan')

@section('content')

<div class="card">
    <h3>Datos del ingrediente</h3>
    <form action="{{ route('admin.ingredientes.update', $ingrediente) }}" method="POST" class="formulario">
        @csrf
        @method('PUT')
        <div class="grid">
            <div class="campo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $ingrediente->nombre) }}" required>
            </div>
            <div class="campo">
                <label>Unidad de medida</label>
                <select name="unidad_medida" required>
                    @foreach(['unidad','g','kg','ml','l'] as $u)
                        <option value="{{ $u }}" @selected($ingrediente->unidad_medida === $u)>{{ $u }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label>Stock actual</label>
                <input type="text" value="{{ $ingrediente->stock_actual }}" disabled>
            </div>
            <div class="campo">
                <label>Stock mínimo</label>
                <input type="number" step="0.01" min="0" name="stock_minimo" value="{{ old('stock_minimo', $ingrediente->stock_minimo) }}" required>
            </div>
        </div>
        <div class="acciones">
            <button type="submit" class="btn guardar">Actualizar</button>
            <a href="{{ route('admin.ingredientes.index') }}" class="btn cancelar">Volver</a>
        </div>
    </form>
</div>

<div class="charts-grid">
    <div class="card" style="margin-bottom:0;">
        <h3>Proveedores de este ingrediente</h3>
        <form action="{{ route('admin.ingredientes.proveedores.asociar', $ingrediente) }}" method="POST" class="formulario form-inline" style="margin-bottom:16px;">
            @csrf
            <div>
                <label>Proveedor</label>
                <select name="id_proveedor" required>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Precio referencia</label>
                <input type="number" step="0.01" min="0" name="precio_referencia">
            </div>
            <button type="submit" class="btn guardar btn-sm">Asociar</button>
        </form>

        <table>
            <tr><th>Proveedor</th><th>Precio ref.</th><th></th></tr>
            @forelse($ingrediente->proveedores as $prov)
            <tr>
                <td>{{ $prov->nombre }}</td>
                <td>{{ $prov->pivot->precio_referencia ? '$'.number_format($prov->pivot->precio_referencia, 0, ',', '.') : '—' }}</td>
                <td>
                    <form action="{{ route('admin.ingredientes.proveedores.quitar', [$ingrediente, $prov]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm desactivar">Quitar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="empty-state">Sin proveedores asociados.</td></tr>
            @endforelse
        </table>
    </div>

    <div class="card" style="margin-bottom:0;">
        <h3>Productos que lo usan (receta)</h3>
        <form action="{{ route('admin.ingredientes.productos.asociar', $ingrediente) }}" method="POST" class="formulario form-inline" style="margin-bottom:16px;">
            @csrf
            <div>
                <label>Producto</label>
                <select name="id_producto" required>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Cantidad requerida</label>
                <input type="number" step="0.01" min="0.01" name="cantidad_requerida" value="1" required>
            </div>
            <button type="submit" class="btn guardar btn-sm">Vincular</button>
        </form>

        <table>
            <tr><th>Producto</th><th>Cantidad</th><th></th></tr>
            @forelse($ingrediente->productos as $prod)
            <tr>
                <td>{{ $prod->nombre }}</td>
                <td>{{ $prod->pivot->cantidad_requerida }} {{ $ingrediente->unidad_medida }}</td>
                <td>
                    <form action="{{ route('admin.ingredientes.productos.quitar', [$ingrediente, $prod]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm desactivar">Quitar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="empty-state">Sin productos vinculados.</td></tr>
            @endforelse
        </table>
    </div>
</div>
@endsection
