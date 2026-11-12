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
            'contrasena' => 'required|string|min:6',

            'id_rol' => [
                'required',
                Rule::exists('roles', 'id_roles')
                    ->where(function ($query) {
                        $query->whereIn('id_roles', [1, 2, 3])
                            ->whereRaw("LOWER(estado) = 'activo'");
                    }),
            ],

            'estado' => 'required|in:activo,inactivo,Activo,Inactivo',
        ], [
            'nombre.required' => 'Debes ingresar el nombre.',
            'documento.required' => 'Debes ingresar el documento.',
            'correo.required' => 'Debes ingresar el correo.',
            'correo.email' => 'El correo no tiene un formato válido.',
            'correo.unique' => 'Este correo ya está registrado.',
            'usuario.required' => 'Debes ingresar un nombre de usuario.',
            'usuario.unique' => 'Este nombre de usuario ya existe.',
            'contrasena.required' => 'Debes ingresar una contraseña.',
            'contrasena.string' => 'La contraseña debe ser texto.',
            'contrasena.min' => 'La contraseña debe tener mínimo 6 caracteres.',
            'id_rol.required' => 'Debes seleccionar un rol.',
            'id_rol.exists' => 'El rol seleccionado no es válido o está inactivo.',
            'estado.required' => 'Debes seleccionar un estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'correo' => $request->correo,
            'usuario' => $request->usuario,
            'contrasena' => Hash::make($request->input('contrasena')),
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
