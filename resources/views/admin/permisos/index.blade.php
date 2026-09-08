@extends('layouts.admin')

@section('title', 'Permisos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/permisos.css') }}">
@endpush

@section('content')
<div class="topbar">
    <h1>Permisos</h1>
    <div>
        <a class="btn-crear" href="{{ route('admin.permisos.create') }}">+ Agregar</a>
        <a class="btn-crear" href="{{ route('admin.permisos.assign') }}">Asignar a Rol</a>
    </div>
</div>

<div class="card">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        @foreach($permisos as $p)
        <tr>
            <td>{{ $p->id_permisos }}</td>
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->descripcion }}</td>
            <td>
                <a href="{{ route('admin.permisos.edit', $p) }}" class="btn">Editar</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
