@extends('layouts.admin')

@section('title', 'Asignar Permisos')
@section('page_title', 'Asignar permisos a roles')

@section('content')

<div class="card">
    <h3>Nueva asignación</h3>
    <form method="POST" action="{{ route('admin.permisos.assign.post') }}" class="formulario form-inline">
        @csrf
        <div>
            <label>Rol</label>
            <select name="id_roles" required>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id_roles }}">{{ $rol->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Permiso</label>
            <select name="id_permisos" required>
                @foreach($permisos as $p)
                    <option value="{{ $p->id_permisos }}">{{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn guardar">Asignar</button>
    </form>
</div>

<div class="tabla">
    <h3>Asignaciones actuales</h3>
    <table>
        <tr><th>Rol</th><th>Permiso</th></tr>
        @forelse($asignados as $a)
        <tr>
            <td>{{ $a->rol_nombre }}</td>
            <td>{{ $a->permiso_nombre }}</td>
        </tr>
        @empty
        <tr><td colspan="2" class="empty-state">Todavía no hay permisos asignados.</td></tr>
        @endforelse
    </table>
</div>
@endsection
