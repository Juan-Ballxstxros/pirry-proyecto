<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')->orderBy('id')->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        // Obtener los tres roles reales de la base de datos.
        $roles = Role::whereIn('id_roles', [1, 2, 3])
            ->whereRaw("LOWER(estado) = 'activo'")
            ->orderBy('id_roles')
            ->get();

        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'documento' => 'required|string|max:50',
            'correo' => 'required|email|unique:usuarios,correo',
            'usuario' => 'required|string|max:50|unique:usuarios,usuario',
            'contrasena' => 'required|min:6',

            'id_rol' => [
                'required',
                Rule::exists('roles', 'id_roles')
                    ->where(function ($query) {
                        $query->whereIn('id_roles', [1, 2, 3])
                            ->whereRaw("LOWER(estado) = 'activo'");
                    }),
            ],

            'estado' => 'required|in:activo,inactivo,Activo,Inactivo',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'correo' => $request->correo,
            'usuario' => $request->usuario,
            'contrasena' => Hash::make($request->contrasena),
            'id_rol' => $request->id_rol,
            'estado' => $request->estado,
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function toggleEstado(Usuario $usuario)
    {
        $activo = strtolower($usuario->estado) === 'activo';

        $usuario->update([
            'estado' => $activo ? 'Inactivo' : 'Activo'
        ]);

        return redirect()->route('admin.usuarios.index');
    }
}
