@extends('layouts.admin')

@section('title', 'Roles')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/roles.css') }}">
@endpush

@section('content')
<h1>Gestión de Roles</h1>

<div class="card">
    <h3>Crear Nuevo Rol</h3>
    <form method="POST" action="{{ route('admin.roles.store') }}" class="formulario">
        @csrf
        <input type="text" name="nombre" placeholder="Nombre del rol" required>
        <input type="text" name="descripcion" placeholder="Descripción" required>
        <button type="submit">Guardar</button>
    </form>
</div>

<div class="card">
    <h3>Roles Registrados</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        @foreach($roles as $rol)
        <tr>
            <td>{{ $rol->id_roles }}</td>
            <td>{{ $rol->nombre }}</td>
            <td>{{ $rol->descripcion }}</td>
            <td class="{{ strtolower($rol->estado) }}">{{ $rol->estado }}</td>
            <td>
                <form action="{{ route('admin.roles.toggle', $rol) }}" method="POST" style="display:inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn">Cambiar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
