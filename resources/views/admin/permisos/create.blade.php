
@extends('layouts.admin')

@section('title', 'Agregar Permiso')
@section('page_title', 'Agregar permiso')
@section('page_subtitle', 'Registra un nuevo permiso para el sistema')

@section('content')

<style>
    .crear-permiso-contenedor {
        width: 100%;
        max-width: 750px;
        margin: 0 auto;
    }

    .crear-permiso-card {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 15px;
        padding: 28px;
        box-shadow: 0 4px 15px rgba(41, 37, 36, .04);
    }

    .crear-permiso-card h3 {
        color: #292524;
        font-size: 19px;
        font-weight: 700;
        margin: 0 0 6px;
    }

    .crear-permiso-card .subtitulo {
        color: #78716c;
        font-size: 13px;
        margin: 0 0 25px;
    }

    .crear-permiso-campo {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }

    .crear-permiso-campo label {
        color: #44403c;
        font-size: 14px;
        font-weight: 600;
    }

    .crear-permiso-campo input,
    .crear-permiso-campo textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 14px;
        border: 1px solid #fed7aa;
        border-radius: 9px;
        color: #292524;
        background: #fff;
        font-size: 14px;
        outline: none;
        transition: .2s ease;
    }

    .crear-permiso-campo textarea {
        min-height: 120px;
        resize: vertical;
    }

    .crear-permiso-campo input:focus,
    .crear-permiso-campo textarea:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
    }

    .crear-permiso-acciones {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #ffedd5;
    }

    .crear-permiso-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 11px 20px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .crear-permiso-guardar {
        background: #f97316;
        color: #fff;
        border: 1px solid #f97316;
    }

    .crear-permiso-guardar:hover {
        background: #ea580c;
    }

    .crear-permiso-cancelar {
        background: #fff;
        color: #57534e;
        border: 1px solid #fed7aa;
    }

    .crear-permiso-cancelar:hover {
        background: #fff7ed;
    }

    @media (max-width: 550px) {
        .crear-permiso-card {
            padding: 20px;
        }

        .crear-permiso-acciones .crear-permiso-btn {
            flex: 1;
        }
    }
</style>

<div class="crear-permiso-contenedor">

    <div class="crear-permiso-card">

        <h3>Información del permiso</h3>
        <p class="subtitulo">
            Completa los datos para registrar un nuevo permiso.
        </p>

        <form method="POST" action="{{ route('admin.permisos.store') }}">
            @csrf

            <div class="crear-permiso-campo">
                <label for="nombre">Nombre del permiso</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    placeholder="Ejemplo: crear_usuarios"
                    required
                >
                @error('nombre')
                    <small style="color:#dc2626;">{{ $message }}</small>
                @enderror
            </div>

            <div class="crear-permiso-campo">
                <label for="descripcion">Descripción</label>
                <textarea
                    id="descripcion"
                    name="descripcion"
                    placeholder="Describe qué permite realizar este permiso..."
                    required
                >{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <small style="color:#dc2626;">{{ $message }}</small>
                @enderror
            </div>

            <div class="crear-permiso-acciones">
                <button type="submit"
                        class="crear-permiso-btn crear-permiso-guardar">
                    Guardar permiso
                </button>

                <a href="{{ route('admin.permisos.index') }}"
                   class="crear-permiso-btn crear-permiso-cancelar">
                    Cancelar
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
