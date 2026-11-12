
@extends('layouts.admin')

@section('title', 'Permisos')
@section('page_title', 'Permisos')
@section('page_subtitle', 'Administra los permisos del sistema y su asignación a roles')

@section('content')

<style>
    .permisos-page {
        width: 100%;
    }

    .permisos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 24px;
    }

    .permisos-descripcion {
        color: #78716c;
        font-size: 14px;
        margin: 5px 0 0;
    }

    .permisos-acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .permisos-acciones .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 11px 17px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s ease;
    }

    .permisos-acciones .btn-outline {
        background: #fff;
        color: #292524;
        border: 1px solid #fed7aa;
    }

    .permisos-acciones .btn-outline:hover {
        background: #fff7ed;
        border-color: #f97316;
    }

    .permisos-acciones .btn-crear {
        background: #f97316;
        color: #fff;
        border: 1px solid #f97316;
    }

    .permisos-acciones .btn-crear:hover {
        background: #ea580c;
        border-color: #ea580c;
    }

    .permisos-card {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(41, 37, 36, .04);
    }

    .permisos-card-header {
        padding: 20px 22px;
        border-bottom: 1px solid #ffedd5;
    }

    .permisos-card-header h3 {
        margin: 0;
        color: #292524;
        font-size: 17px;
        font-weight: 700;
    }

    .permisos-card-header p {
        margin: 5px 0 0;
        color: #78716c;
        font-size: 13px;
    }

    .permisos-tabla-contenedor {
        width: 100%;
        overflow-x: auto;
    }

    .permisos-tabla {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .permisos-tabla th {
        background: #fff7ed;
        color: #57534e;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
        padding: 15px 20px;
        text-align: left;
    }

    .permisos-tabla td {
        padding: 17px 20px;
        border-top: 1px solid #f5f5f4;
        color: #44403c;
        font-size: 14px;
    }

    .permisos-tabla tbody tr:hover {
        background: #fffbf7;
    }

    .permiso-id {
        color: #78716c;
        font-weight: 600;
    }

    .permiso-nombre {
        color: #292524;
        font-weight: 700;
    }

    .permiso-descripcion {
        color: #78716c;
    }

    .btn-editar-permiso {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s ease;
    }

    .btn-editar-permiso:hover {
        background: #f97316;
        color: #fff;
        border-color: #f97316;
    }

    .permisos-vacio {
        text-align: center !important;
        padding: 40px 20px !important;
        color: #78716c !important;
    }

    .permisos-vacio-icono {
        display: block;
        font-size: 30px;
        margin-bottom: 10px;
    }

    @media (max-width: 600px) {
        .permisos-header {
            align-items: stretch;
        }

        .permisos-acciones {
            width: 100%;
        }

        .permisos-acciones .btn {
            flex: 1;
        }
    }
</style>

<div class="permisos-page">

    <div class="permisos-header">
        <div>
            <p class="permisos-descripcion">
                Gestiona los accesos y las autorizaciones del sistema.
            </p>
        </div>

        <div class="permisos-acciones">
            <a class="btn btn-outline"
               href="{{ route('admin.permisos.assign') }}">
                Asignar a rol
            </a>

            <a class="btn btn-crear"
               href="{{ route('admin.permisos.create') }}">
                + Agregar permiso
            </a>
        </div>
    </div>

    <div class="permisos-card">

        <div class="permisos-card-header">
            <h3>Listado de permisos</h3>
            <p>Consulta y administra los permisos registrados.</p>
        </div>

        <div class="permisos-tabla-contenedor">
            <table class="permisos-tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($permisos as $p)
                        <tr>
                            <td>
                                <span class="permiso-id">
                                    #{{ $p->id_permisos }}
                                </span>
                            </td>

                            <td>
                                <span class="permiso-nombre">
                                    {{ $p->nombre }}
                                </span>
                            </td>

                            <td>
                                <span class="permiso-descripcion">
                                    {{ $p->descripcion ?: 'Sin descripción' }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('admin.permisos.edit', $p) }}"
                                   class="btn-editar-permiso">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="permisos-vacio">
                                <span class="permisos-vacio-icono">🔐</span>
                                No hay permisos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
