@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page_title', 'Usuarios')
@section('page_subtitle', $usuarios->count() . ' usuarios registrados')

@section('content')
<div class="topbar">
    <div></div>
    <a class="btn btn-crear" href="{{ route('admin.usuarios.create') }}">+ Registrar usuario</a>
</div>

<div class="tabla">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        @forelse($usuarios as $u)
        <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->nombre }}</td>
            <td>{{ $u->usuario }}</td>
            <td>{{ $u->rol?->nombre ?? $u->id_rol }}</td>
            <td>
                <span class="pill {{ strtolower($u->estado) === 'activo' ? 'pill-activo' : 'pill-inactivo' }}">
                    {{ ucfirst($u->estado) }}
                </span>
            </td>
            <td>
                <form action="{{ route('admin.usuarios.toggle', $u) }}" method="POST" style="display:inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ strtolower($u->estado) === 'activo' ? 'desactivar' : 'activar' }}">
                        {{ strtolower($u->estado) === 'activo' ? 'Inhabilitar' : 'Habilitar' }}
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty-state">No hay usuarios registrados.</td></tr>
        @endforelse
    </table>
</div>
@endsection
