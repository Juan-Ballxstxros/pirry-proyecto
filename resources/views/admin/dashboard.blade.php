@extends('layouts.admin')

@section('title', 'Panel Administrador')

@section('content')
<div class="topbar">
    <div class="user">Bienvenido {{ Auth::user()->nombre }}</div>
</div>

<div class="cards">
    <div class="card">Usuarios<br><strong>{{ $totalUsuarios }}</strong></div>
    <div class="card">Roles<br><strong>{{ $totalRoles }}</strong></div>
    <div class="card">Permisos<br><strong>{{ $totalPermisos }}</strong></div>
    <div class="card">Activos<br><strong>{{ $porcentaje }}%</strong></div>
</div>

<div class="tabla">
    <h3>Últimos usuarios</h3>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Estado</th>
        </tr>
        @forelse($ultimos as $u)
        <tr>
            <td>{{ $u->nombre }}</td>
            <td class="{{ strtolower($u->estado) === 'activo' ? 'activo' : 'pendiente' }}">{{ ucfirst($u->estado) }}</td>
        </tr>
        @empty
        <tr><td colspan="2">No hay datos</td></tr>
        @endforelse
    </table>
</div>
@endsection
