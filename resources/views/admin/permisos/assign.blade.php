
@extends('layouts.admin')

@section('title', 'Asignar Permisos')
@section('page_title', 'Asignar permisos a roles')
@section('page_subtitle', 'Administra los permisos que tiene cada rol')

@section('content')

<style>
    .permisos-form-page {
        width: 100%;
    }

    .permisos-form-card {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(41, 37, 36, .04);
    }

    .permisos-form-card h3 {
        color: #292524;
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 6px;
    }

    .permisos-form-card .subtitulo {
        color: #78716c;
        font-size: 13px;
        margin: 0 0 22px;
    }

    .permisos-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .permisos-campo {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .permisos-campo label {
        color: #44403c;
        font-size: 14px;
        font-weight: 600;
    }

    .permisos-campo select {
        width: 100%;
        min-height: 45px;
        padding: 10px 12px;
        border: 1px solid #fed7aa;
        border-radius: 9px;
        background: #fff;
        color: #292524;
        outline: none;
    }

    .permisos-campo select:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
    }

    .permisos-form-acciones {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .permisos-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 20px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .permisos-btn-principal {
        color: #fff;
        background: #f97316;
        border: 1px solid #f97316;
    }

    .permisos-btn-principal:hover {
        background: #ea580c;
    }

    .permisos-btn-secundario {
        color: #57534e;
        background: #fff;
        border: 1px solid #fed7aa;
    }

    .permisos-btn-secundario:hover {
        background: #fff7ed;
    }

    .permisos-tabla-card {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(41, 37, 36, .04);
    }

    .permisos-tabla-titulo {
        padding: 20px 22px;
        border-bottom: 1px solid #ffedd5;
    }

    .permisos-tabla-titulo h3 {
        margin: 0;
        color: #292524;
        font-size: 17px;
        font-weight: 700;
    }

    .permisos-tabla-titulo p {
        margin: 5px 0 0;
        color: #78716c;
        font-size: 13px;
    }

    .permisos-tabla-scroll {
        overflow-x: auto;
    }

    .permisos-tabla {
        width: 100%;
        min-width: 400px;
        border-collapse: collapse;
    }

    .permisos-tabla th {
        padding: 15px 20px;
        background: #fff7ed;
        color: #57534e;
        text-align: left;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .permisos-tabla td {
        padding: 16px 20px;
        border-top: 1px solid #f5f5f4;
        color: #44403c;
        font-size: 14px;
    }

    .permisos-tabla tbody tr:hover {
        background: #fffbf7;
    }

    .permisos-vacio {
        text-align: center;
        padding: 30px !important;
        color: #78716c !important;
    }

    @media (max-width: 650px) {
        .permisos-form-grid {
            grid-template-columns: 1fr;
        }

        .permisos-form-card {
            padding: 18px;
        }
    }
</style>

<div class="permisos-form-page">

    <div class="permisos-form-card">
        <h3>Nueva asignación</h3>
        <p class="subtitulo">
            Selecciona un rol y el permiso que deseas asignarle.
        </p>

        <form method="POST"
              action="{{ route('admin.permisos.assign.post') }}">
            @csrf

            <div class="permisos-form-grid">

                <div class="permisos-campo">
                    <label for="id_roles">Rol</label>
                    <select name="id_roles" id="id_roles" required>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_roles }}"
                                {{ old('id_roles') == $rol->id_roles ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="permisos-campo">
                    <label for="id_permisos">Permiso</label>
                    <select name="id_permisos" id="id_permisos" required>
                        @foreach($permisos as $p)
                            <option value="{{ $p->id_permisos }}"
                                {{ old('id_permisos') == $p->id_permisos ? 'selected' : '' }}>
                                {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="permisos-form-acciones">
                <button type="submit" class="permisos-btn permisos-btn-principal">
                    Asignar permiso
                </button>

                <a href="{{ route('admin.permisos.index') }}"
                   class="permisos-btn permisos-btn-secundario">
                    Volver
                </a>
            </div>
        </form>
    </div>

    <div class="permisos-tabla-card">

        <div class="permisos-tabla-titulo">
            <h3>Asignaciones actuales</h3>
            <p>Relación de roles y permisos registrados en el sistema.</p>
        </div>

        <div class="permisos-tabla-scroll">
            <table class="permisos-tabla">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Permiso</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($asignados as $a)
                        <tr>
                            <td>{{ $a->rol_nombre }}</td>
                            <td>{{ $a->permiso_nombre }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="permisos-vacio">
                                Todavía no hay permisos asignados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
