<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    // RF-034 · RI-034
    public function index()
    {
        $proveedores = Proveedor::withCount('compras')->orderBy('nombre')->get();

        return view('admin.proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('admin.proveedores.create');
    }

    // RN-020: no se permiten dos proveedores con la misma identificación o correo.
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'identificacion' => 'required|string|max:30|unique:proveedores,identificacion',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:150',
            'email' => 'required|email|max:100|unique:proveedores,email',
        ], [
            'identificacion.unique' => 'Ya existe un proveedor con esa identificación.',
            'email.unique' => 'Ya existe un proveedor con ese correo.',
        ]);

        Proveedor::create([
            'nombre' => $request->nombre,
            'identificacion' => $request->identificacion,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'email' => $request->email,
            'estado' => 'activo', // RN-020: estado inicial siempre "activo"
        ]);

        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('admin.proveedores.edit', compact('proveedor'));
    }

    // RF-035 · RI-035 · RN-021: el ID nunca se modifica.
    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:150',
            'email' => 'required|email|max:100|unique:proveedores,email,'.$proveedor->id,
        ]);

        $proveedor->update($request->only('nombre', 'telefono', 'direccion', 'email'));

        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    // RF-035(036) · RN-022: "eliminar" = cambiar estado a inactivo, nunca borrar filas.
    // No se puede inhabilitar un proveedor con compras activas (trazabilidad).
    public function toggleEstado(Proveedor $proveedor)
    {
        if ($proveedor->isActive() && $proveedor->compras()->count() > 0) {
            return back()->with('error', 'No se puede inhabilitar: el proveedor tiene compras registradas en el sistema.');
        }

        $proveedor->estado = $proveedor->isActive() ? 'inactivo' : 'activo';
        $proveedor->save();

        return back()->with('success', 'Estado del proveedor actualizado.');
    }
}
