<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Usuario;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsuarios = Usuario::count();
        $totalRoles = Role::count();
        $totalPermisos = Permission::count();
        $totalActivos = Usuario::whereRaw("LOWER(estado) = 'activo'")->count();
        $porcentaje = $totalUsuarios > 0 ? round(($totalActivos / $totalUsuarios) * 100) : 0;

        $totalProductos = Product::count();
        $productosActivos = Product::whereRaw("LOWER(estado) = 'activo'")->count();

        $totalPedidos = Order::count();
        $ingresosTotales = (float) Order::sum('total');

        $hoy = Carbon::today();
        $pedidosHoy = Order::whereDate('fecha', $hoy)->count();
        $ingresosHoy = (float) Order::whereDate('fecha', $hoy)->sum('total');

        $ultimos = Usuario::orderByDesc('id')->limit(5)->get(['id', 'nombre', 'estado']);
        $ultimosPedidos = Order::with('usuario')->orderByDesc('fecha')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsuarios', 'totalRoles', 'totalPermisos', 'totalActivos', 'porcentaje',
            'totalProductos', 'productosActivos', 'totalPedidos', 'ingresosTotales',
            'pedidosHoy', 'ingresosHoy', 'ultimos', 'ultimosPedidos'
        ));
    }
}
