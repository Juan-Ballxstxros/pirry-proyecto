<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsuarios = Usuario::count();
        $totalRoles = Role::count();
        $totalPermisos = Permission::count();
        $totalActivos = Usuario::whereRaw("LOWER(estado) = 'activo'")->count();
        $porcentaje = $totalUsuarios > 0 ? round(($totalActivos / $totalUsuarios) * 100) : 0;

        $ultimos = Usuario::orderByDesc('id')->limit(5)->get(['id', 'nombre', 'estado']);

        return view('admin.dashboard', compact(
            'totalUsuarios', 'totalRoles', 'totalPermisos', 'totalActivos', 'porcentaje', 'ultimos'
        ));
    }
}
