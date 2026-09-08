@extends('layouts.admin')

@section('title', 'Asignar Permisos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/as_permisos.css') }}">
@endpush

@section('content')
<h1>Asignar Permisos a Roles</h1>
<div class="card">
    <form method="POST" action="{{ route('admin.permisos.assign.post') }}">
        @csrf
        <label>Rol</label>
        <select name="id_roles" required>
            @foreach($roles as $rol)
                <option value="{{ $rol->id_roles }}">{{ $rol->nombre }}</option>
            @endforeach
        </select>
        <label>Permiso</label>
        <select name="id_permisos" required>
            @foreach($permisos as $p)
                <option value="{{ $p->id_permisos }}">{{ $p->nombre }}</option>
            @endforeach
        </select>
        <button type="submit">Asignar</button>
    </form>
</div>

<div class="card">
    <h3>Asignaciones actuales</h3>
    <table>
        <tr><th>Rol ID</th><th>Permiso ID</th></tr>
        @foreach($asignados as $a)
        <tr><td>{{ $a->id_roles }}</td><td>{{ $a->id_permisos }}</td></tr>
        @endforeach
    </table>
</div>
@endsection
