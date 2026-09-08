@extends('layouts.admin')

@section('title', 'Permisos')
@section('page_title', 'Permisos')
@section('page_subtitle', 'Permisos del sistema y su asignación a roles')

@section('content')
<div class="topbar">
    <div></div>
    <div style="display:flex; gap:10px;">
        <a class="btn btn-outline" href="{{ route('admin.permisos.assign') }}">Asignar a rol</a>
        <a class="btn btn-crear" href="{{ route('admin.permisos.create') }}">+ Agregar permiso</a>
    </div>
</div>

<div class="tabla">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        @forelse($permisos as $p)
        <tr>
            <td>{{ $p->id_permisos }}</td>
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->descripcion }}</td>
            <td>
                <a href="{{ route('admin.permisos.edit', $p) }}" class="btn btn-sm btn-outline">Editar</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="empty-state">No hay permisos registrados.</td></tr>
        @endforelse
    </table>
</div>
@endsection
