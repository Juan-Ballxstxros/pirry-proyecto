@extends('layouts.admin')

@section('title', 'Editar Permiso')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ed_permisos.css') }}">
@endpush

@section('content')
<h1>Editar Permiso</h1>
<div class="card">
    <form method="POST" action="{{ route('admin.permisos.update', $permiso) }}">
        @csrf
        @method('PUT')
        <label>Nombre</label>
        <input type="text" name="nombre" value="{{ $permiso->nombre }}" required>
        <label>Descripción</label>
        <textarea name="descripcion" required>{{ $permiso->descripcion }}</textarea>
        <button type="submit">Actualizar</button>
        <a href="{{ route('admin.permisos.index') }}">Cancelar</a>
    </form>
</div>
@endsection
