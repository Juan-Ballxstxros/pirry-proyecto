<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('id_roles')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:25',
            'descripcion' => 'required|string',
        ]);

        Role::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado' => 'Activo',
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado.');
    }

    public function toggleEstado(Role $role)
    {
        $activo = $role->estado === 'Activo';
        $role->update(['estado' => $activo ? 'Inactivo' : 'Activo']);

        return redirect()->route('admin.roles.index');
    }
}
