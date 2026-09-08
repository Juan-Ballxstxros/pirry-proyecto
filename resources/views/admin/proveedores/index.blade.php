@extends('layouts.admin')

@section('title', 'Proveedores')
@section('page_title', 'Proveedores')
@section('page_subtitle', $proveedores->count() . ' proveedores registrados')

@section('content')
<div class="topbar">
    <div></div>
    <a class="btn btn-crear" href="{{ route('admin.proveedores.create') }}">+ Registrar proveedor</a>
</div>

<div class="tabla">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Identificación</th>
            <th>Contacto</th>
            <th>Compras</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        @forelse($proveedores as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->identificacion }}</td>
            <td>{{ $p->email }}<br><span class="text-muted">{{ $p->telefono ?: '—' }}</span></td>
            <td>{{ $p->compras_count }}</td>
            <td>
                <span class="pill {{ strtolower($p->estado) === 'activo' ? 'pill-activo' : 'pill-inactivo' }}">
                    {{ ucfirst($p->estado) }}
                </span>
            </td>
            <td style="display:flex; gap:6px;">
                <a href="{{ route('admin.proveedores.edit', $p) }}" class="btn btn-sm btn-outline">Editar</a>
                <form action="{{ route('admin.proveedores.toggle', $p) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ strtolower($p->estado) === 'activo' ? 'desactivar' : 'activar' }}">
                        {{ strtolower($p->estado) === 'activo' ? 'Inhabilitar' : 'Habilitar' }}
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="empty-state">No hay proveedores registrados.</td></tr>
        @endforelse
    </table>
</div>
@endsection
