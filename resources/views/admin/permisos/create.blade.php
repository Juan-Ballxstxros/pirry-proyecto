@extends('layouts.admin')

@section('title', 'Agregar Permiso')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ag_permisos.css') }}">
@endpush

@section('content')
<h1>Agregar Permiso</h1>
<div class="card">
    <form method="POST" action="{{ route('admin.permisos.store') }}">
        @csrf
        <label>Nombre</label>
        <input type="text" name="nombre" required>
        <label>Descripción</label>
        <textarea name="descripcion" required></textarea>
        <button type="submit">Guardar</button>
        <a href="{{ route('admin.permisos.index') }}">Cancelar</a>
    </form>
</div>
@endsection
