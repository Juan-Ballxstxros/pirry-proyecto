
@extends('layouts.admin')

@section('title', 'Roles')
@section('page_title', 'Roles')
@section('page_subtitle', 'Gestión de roles del sistema')

@section('content')

<style>
    .roles-page {
        --pirry-orange: #F97316;
        --pirry-dark: #292524;
        --pirry-gray: #78716C;
        --pirry-border: #FED7AA;
        color: var(--pirry-dark);
        background: #FFFFFF;
        width: 100%;
    }

    .roles-page * {
        box-sizing: border-box;
    }

    .roles-page .roles-card {
        background: #FFFFFF;
        border: 1px solid var(--pirry-border);
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: 0 3px 12px rgba(41, 37, 36, 0.04);
    }

    .roles-page .section-heading {
        margin: 0 0 20px;
        font-size: 19px;
        font-weight: 700;
        color: var(--pirry-dark);
    }

    .roles-page .section-description {
        color: var(--pirry-gray);
        font-size: 13px;
        margin: -12px 0 20px;
    }

    .roles-page .role-form {
        display: grid;
        grid-template-columns: 1fr 1.5fr auto;
        align-items: end;
        gap: 18px;
    }

    .roles-page .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .roles-page .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: var(--pirry-dark);
    }

    .roles-page .form-group input {
        width: 100%;
        min-height: 43px;
        padding: 11px 13px;
        border: 1px solid var(--pirry-border);
        border-radius: 8px;
        background: #FFFFFF;
        color: var(--pirry-dark);
        font-size: 14px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .roles-page .form-group input:focus {
        border-color: var(--pirry-orange);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .10);
    }

    .roles-page .form-group input::placeholder {
        color: var(--pirry-gray);
    }

    .roles-page .btn-save {
        min-height: 43px;
        padding: 0 22px;
        border: 1px solid var(--pirry-orange);
        border-radius: 8px;
        background: var(--pirry-orange);
        color: #FFFFFF;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s, transform .2s;
        white-space: nowrap;
    }

    .roles-page .btn-save:hover {
        background: #EA580C;
        transform: translateY(-1px);
    }

    .roles-page .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .roles-page .table-header .section-heading {
        margin: 0;
    }

    .roles-page .role-count {
        background: #FFF7ED;
        color: var(--pirry-orange);
        border: 1px solid var(--pirry-border);
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .roles-page .table-container {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--pirry-border);
        border-radius: 10px;
    }

    .roles-page table {
        width: 100%;
        min-width: 650px;
        border-collapse: collapse;
        background: #FFFFFF;
    }

    .roles-page table th {
        padding: 15px 16px;
        background: #FFF7ED;
        color: var(--pirry-dark);
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        border-bottom: 1px solid var(--pirry-border);
    }

    .roles-page table td {
        padding: 16px;
        font-size: 13px;
        color: var(--pirry-dark);
        border-bottom: 1px solid #FED7AA;
        vertical-align: middle;
    }

    .roles-page table tr:last-child td {
        border-bottom: none;
    }

    .roles-page table tbody tr:hover {
        background: #FFF7ED;
    }

    .roles-page .role-id {
        color: var(--pirry-gray);
        font-weight: 600;
    }

    .roles-page .role-name {
        font-weight: 700;
        color: var(--pirry-dark);
    }

    .roles-page .role-description {
        color: var(--pirry-gray);
    }

    .roles-page .pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .roles-page .pill-activo {
        background: #DCFCE7;
        color: #15803D;
    }

    .roles-page .pill-inactivo {
        background: #FEE2E2;
        color: #DC2626;
    }

    .roles-page .btn-toggle {
        padding: 8px 13px;
        border: 1px solid var(--pirry-orange);
        border-radius: 7px;
        background: #FFFFFF;
        color: var(--pirry-orange);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }

    .roles-page .btn-toggle:hover {
        background: var(--pirry-orange);
        color: #FFFFFF;
    }

    .roles-page .empty-state {
        text-align: center;
        padding: 35px 15px;
        color: var(--pirry-gray);
        font-size: 14px;
    }

    @media (max-width: 850px) {
        .roles-page .role-form {
            grid-template-columns: 1fr 1fr;
        }

        .roles-page .btn-save {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 550px) {
        .roles-page .roles-card {
            padding: 17px;
        }

        .roles-page .role-form {
            grid-template-columns: 1fr;
        }

        .roles-page .btn-save {
            grid-column: auto;
            width: 100%;
        }

        .roles-page .table-header {
            align-items: flex-start;
        }
    }
</style>

<div class="roles-page">

    {{-- CREAR NUEVO ROL --}}
    <div class="roles-card">

        <h3 class="section-heading">Crear nuevo rol</h3>

        <p class="section-description">
            Registra un nuevo rol para administrar los accesos del sistema.
        </p>

        <form method="POST"
              action="{{ route('admin.roles.store') }}"
              class="role-form">

            @csrf

            <div class="form-group">
                <label for="nombre">Nombre</label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Ej: Vendedor"
                    value="{{ old('nombre') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>

                <input
                    type="text"
                    id="descripcion"
                    name="descripcion"
                    placeholder="Breve descripción del rol"
                    value="{{ old('descripcion') }}"
                    required
                >
            </div>

            <button type="submit" class="btn-save">
                + Guardar rol
            </button>

        </form>

    </div>

    {{-- ROLES REGISTRADOS --}}
    <div class="roles-card">

        <div class="table-header">

            <h3 class="section-heading">
                Roles registrados
            </h3>

            <span class="role-count">
                {{ $roles->count() }} roles
            </span>

        </div>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($roles as $rol)

                    <tr>

                        <td class="role-id">
                            #{{ $rol->id_roles }}
                        </td>

                        <td class="role-name">
                            {{ $rol->nombre }}
                        </td>

                        <td class="role-description">
                            {{ $rol->descripcion }}
                        </td>

                        <td>
                            <span class="pill {{ strtolower($rol->estado) === 'activo' ? 'pill-activo' : 'pill-inactivo' }}">
                                {{ $rol->estado }}
                            </span>
                        </td>

                        <td>

                            <form
                                action="{{ route('admin.roles.toggle', $rol) }}"
                                method="POST"
                                style="display:inline"
                            >

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn-toggle"
                                >
                                    Cambiar estado
                                </button>

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="5" class="empty-state">
                            No hay roles registrados.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
