@extends('layouts.admin')

@section('title', 'Usuarios')

@section('content')
<div class="topbar">
    <h1>Usuarios Registrados</h1>
    <a class="btn-crear" href="{{ route('admin.usuarios.create') }}">+ Registrar</a>
</div>

<div class="card">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        @foreach($usuarios as $u)
        <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->nombre }}</td>
            <td>{{ $u->usuario }}</td>
            <td>{{ $u->rol?->nombre ?? $u->id_rol }}</td>
            <td class="{{ strtolower($u->estado) }}">{{ $u->estado }}</td>
            <td>
                <form action="{{ route('admin.usuarios.toggle', $u) }}" method="POST" style="display:inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ strtolower($u->estado) === 'activo' ? 'desactivar' : 'activar' }}">
                        {{ strtolower($u->estado) === 'activo' ? 'Inhabilitar' : 'Habilitar' }}
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
