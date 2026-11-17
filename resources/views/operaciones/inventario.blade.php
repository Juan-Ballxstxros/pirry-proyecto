@extends($layout)

@section('title', 'Inventario')
@section('page_title', 'Inventario de ingredientes')
@section('page_subtitle', 'Consulta de stock e ingredientes disponibles por proveedor')

@section('content')
<div class="tabla">
    <table>
        <tr>
            <th>Ingrediente</th>
            <th>Stock actual</th>
            <th>Stock mínimo</th>
            <th>Proveedores</th>
            <th>Estado</th>
        </tr>
        @forelse($ingredientes as $ing)
        <tr>
            <td>{{ $ing->nombre }}</td>
            <td>{{ $ing->stock_actual }} {{ $ing->unidad_medida }}</td>
            <td>{{ $ing->stock_minimo }} {{ $ing->unidad_medida }}</td>
            <td>{{ $ing->proveedores->pluck('nombre')->join(', ') ?: '—' }}</td>
            <td>
                @if($ing->stockBajo())
                    <span class="pill pill-danger">Stock bajo</span>
                @else
                    <span class="pill pill-activo">OK</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-state">No hay ingredientes registrados.</td></tr>
        @endforelse
    </table>
</div>
@endsection