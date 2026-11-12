
@extends('layouts.admin')

@section('title', 'Proveedores')
@section('page_title', 'Proveedores')
@section('page_subtitle', $proveedores->count() . ' proveedores registrados')

@section('content')

<style>
    .pirry-page { width: 100%; }
    .pirry-top {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 22px;
    }
    .pirry-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 10px 16px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
        white-space: nowrap;
    }
    .pirry-btn-primary {
        color: white;
        background: #f97316;
        border: 1px solid #f97316;
    }
    .pirry-btn-primary:hover { background: #ea580c; }
    .pirry-btn-outline {
        color: #c2410c;
        background: #fff7ed;
        border: 1px solid #fed7aa;
    }
    .pirry-btn-outline:hover {
        color: white;
        background: #f97316;
        border-color: #f97316;
    }
    .pirry-btn-danger {
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }
    .pirry-btn-danger:hover {
        color: white;
        background: #dc2626;
    }
    .pirry-btn-success {
        color: #15803d;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }
    .pirry-btn-success:hover {
        color: white;
        background: #15803d;
    }
    .pirry-card {
        background: white;
        border: 1px solid #fed7aa;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(41,37,36,.04);
    }
    .pirry-card-head {
        padding: 20px 22px;
        border-bottom: 1px solid #ffedd5;
    }
    .pirry-card-head h3 {
        margin: 0;
        color: #292524;
        font-size: 17px;
        font-weight: 700;
    }
    .pirry-card-head p {
        margin: 5px 0 0;
        color: #78716c;
        font-size: 13px;
    }
    .pirry-table-wrap { overflow-x: auto; }
    .pirry-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }
    .pirry-table th {
        padding: 15px 17px;
        text-align: left;
        background: #fff7ed;
        color: #57534e;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .pirry-table td {
        padding: 15px 17px;
        border-top: 1px solid #f5f5f4;
        color: #44403c;
        font-size: 13px;
        vertical-align: middle;
    }
    .pirry-table tbody tr:hover { background: #fffbf7; }
    .pirry-main-text { color: #292524; font-weight: 700; }
    .pirry-muted { color: #78716c; font-size: 12px; }
    .pirry-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .pirry-active { color: #15803d; background: #dcfce7; }
    .pirry-inactive { color: #b91c1c; background: #fee2e2; }
    .pirry-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pirry-actions form { margin: 0; }
    .pirry-empty {
        padding: 40px 20px !important;
        text-align: center;
        color: #78716c !important;
    }
    @media(max-width:600px) {
        .pirry-top { justify-content: stretch; }
        .pirry-top .pirry-btn { width: 100%; }
    }
</style>

<div class="pirry-page">
    <div class="pirry-top">
        <a class="pirry-btn pirry-btn-primary"
           href="{{ route('admin.proveedores.create') }}">
            + Registrar proveedor
        </a>
    </div>

    <div class="pirry-card">
        <div class="pirry-card-head">
            <h3>Directorio de proveedores</h3>
            <p>Consulta y administra tus proveedores y sus datos de contacto.</p>
        </div>

        <div class="pirry-table-wrap">
            <table class="pirry-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Identificación</th>
                        <th>Contacto</th>
                        <th>Compras</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td><span class="pirry-main-text">{{ $p->nombre }}</span></td>
                            <td>{{ $p->identificacion }}</td>
                            <td>
                                <div>{{ $p->email }}</div>
                                <div class="pirry-muted">{{ $p->telefono ?: '—' }}</div>
                            </td>
                            <td>{{ $p->compras_count }}</td>
                            <td>
                                <span class="pirry-status {{ strtolower($p->estado) === 'activo' ? 'pirry-active' : 'pirry-inactive' }}">
                                    {{ ucfirst($p->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="pirry-actions">
                                    <a href="{{ route('admin.proveedores.edit', $p) }}"
                                       class="pirry-btn pirry-btn-outline">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.proveedores.toggle', $p) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="pirry-btn {{ strtolower($p->estado) === 'activo' ? 'pirry-btn-danger' : 'pirry-btn-success' }}">
                                            {{ strtolower($p->estado) === 'activo' ? 'Inhabilitar' : 'Habilitar' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="pirry-empty">
                                No hay proveedores registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
