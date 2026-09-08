@extends('layouts.admin')

@section('title', 'Roles')
@section('page_title', 'Roles')
@section('page_subtitle', 'Gestión de roles del sistema')

@section('content')

<div class="card">
    <h3>Crear nuevo rol</h3>
    <form method="POST" action="{{ route('admin.roles.store') }}" class="formulario form-inline">
        @csrf
        <div>
            <label>Nombre</label>
            <input type="text" name="nombre" placeholder="Ej: Vendedor" required>
        </div>
        <div>
            <label>Descripción</label>
            <input type="text" name="descripcion" placeholder="Breve descripción del rol" required>
        </div>
        <button type="submit" class="btn guardar">Guardar</button>
    </form>
</div>

<div class="tabla">
    <h3>Roles registrados</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        @forelse($roles as $rol)
        <tr>
            <td>{{ $rol->id_roles }}</td>
            <td>{{ $rol->nombre }}</td>
            <td>{{ $rol->descripcion }}</td>
            <td>
                <span class="pill {{ strtolower($rol->estado) === 'activo' ? 'pill-activo' : 'pill-inactivo' }}">
                    {{ $rol->estado }}
                </span>
            </td>
            <td>
                <form action="{{ route('admin.roles.toggle', $rol) }}" method="POST" style="display:inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline">Cambiar estado</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-state">No hay roles registrados.</td></tr>
        @endforelse
    </table>
</div>
@endsection
