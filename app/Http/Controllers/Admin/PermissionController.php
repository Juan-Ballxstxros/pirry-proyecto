<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index()
    {
        $permisos = Permission::orderBy('id_permisos')->get();

        return view('admin.permisos.index', compact('permisos'));
    }

    public function create()
    {
        return view('admin.permisos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:25',
            'descripcion' => 'required|string',
        ]);

        Permission::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.permisos.index')->with('success', 'Permiso creado.');
    }

    public function edit(Permission $permiso)
    {
        return view('admin.permisos.edit', compact('permiso'));
    }

    public function update(Request $request, Permission $permiso)
    {
        $request->validate([
            'nombre' => 'required|string|max:25',
            'descripcion' => 'required|string',
        ]);

        $permiso->update($request->only('nombre', 'descripcion'));

        return redirect()->route('admin.permisos.index')->with('success', 'Permiso actualizado.');
    }

    public function assignForm()
    {
        $roles = Role::all();
        $permisos = Permission::all();
        $asignados = DB::table('rol_permiso')->get();

        return view('admin.permisos.assign', compact('roles', 'permisos', 'asignados'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'id_roles' => 'required|exists:roles,id_roles',
            'id_permisos' => 'required|exists:permisos,id_permisos',
        ]);

        DB::table('rol_permiso')->updateOrInsert(
            ['id_roles' => $request->id_roles, 'id_permisos' => $request->id_permisos],
            ['id_roles' => $request->id_roles, 'id_permisos' => $request->id_permisos]
        );

        return redirect()->route('admin.permisos.assign')->with('success', 'Permiso asignado al rol.');
    }
}
