<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (! $usuario) {
            return back()->withErrors(['correo' => 'Usuario no encontrado.'])->withInput();
        }

        if (! $usuario->isActive()) {
            return back()->withErrors(['correo' => 'Tu cuenta está inactiva.'])->withInput();
        }

        $validPassword = $request->contrasena === $usuario->contrasena
            || Hash::check($request->contrasena, $usuario->contrasena);

        if (! $validPassword) {
            return back()->withErrors(['contrasena' => 'Contraseña incorrecta.'])->withInput();
        }

        Auth::login($usuario);

        if ($usuario->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home')->with('success', '¡Bienvenido, '.$usuario->nombre.'!');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'documento' => 'required|string|max:50',
            'correo' => 'required|email|unique:usuarios,correo',
            'usuario' => 'required|string|max:50|unique:usuarios,usuario',
            'contrasena' => ['required', 'confirmed', Password::min(6)],
        ], [
            'correo.unique' => 'Este correo ya está registrado.',
            'usuario.unique' => 'Este nombre de usuario ya existe.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $clienteRol = \App\Models\Role::where('nombre', 'Cliente')->first();
        $rolId = $clienteRol ? $clienteRol->id_roles : 2;

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'correo' => $request->correo,
            'usuario' => $request->usuario,
            'contrasena' => Hash::make($request->contrasena),
            'id_rol' => $rolId,
            'estado' => 'activo',
        ]);

        Auth::login($usuario);

        return redirect()->route('home')->with('success', '¡Cuenta creada exitosamente!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
