@extends('layouts.admin')

@section('title', 'Ingredientes')
@section('page_title', 'Ingredientes')
@section('page_subtitle', 'Catálogo de insumos y su stock')

@section('content')
<div class="topbar">
    <div></div>
    <a class="btn btn-crear" href="{{ route('admin.ingredientes.create') }}">+ Nuevo ingrediente</a>
</div>

<div class="tabla">
    <table>
        <tr>
            <th>Nombre</th>
            <th>Stock actual</th>
            <th>Stock mínimo</th>
            <th>Proveedores</th>
            <th>Productos</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        @forelse($ingredientes as $ing)
        <tr>
            <td>{{ $ing->nombre }}</td>
            <td>
                {{ $ing->stock_actual }} {{ $ing->unidad_medida }}
                @if($ing->stockBajo())
                    <span class="pill pill-danger" style="margin-left:6px;">Bajo</span>
                @endif
            </td>
            <td>{{ $ing->stock_minimo }} {{ $ing->unidad_medida }}</td>
            <td>{{ $ing->proveedores_count }}</td>
            <td>{{ $ing->productos_count }}</td>
            <td>
                <span class="pill {{ strtolower($ing->estado) === 'activo' ? 'pill-activo' : 'pill-inactivo' }}">
                    {{ ucfirst($ing->estado) }}
                </span>
            </td>
            <td style="display:flex; gap:6px;">
                <a href="{{ route('admin.ingredientes.edit', $ing) }}" class="btn btn-sm btn-outline">Gestionar</a>
                <form action="{{ route('admin.ingredientes.toggle', $ing) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ strtolower($ing->estado) === 'activo' ? 'desactivar' : 'activar' }}">
                        {{ strtolower($ing->estado) === 'activo' ? 'Inhabilitar' : 'Habilitar' }}
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="empty-state">No hay ingredientes registrados.</td></tr>
        @endforelse
    </table>
</div>
@endsection
