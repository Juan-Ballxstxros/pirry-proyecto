<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use App\Models\Ingrediente;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $pedidosPendientes = Order::where('estado', 'en_proceso')->count();
        $ventasHoy = Order::whereDate('fecha', Carbon::today())->count();
        $ingredientesBajos = Ingrediente::where('estado', 'activo')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->count();

        $ultimosPedidos = Order::with('usuario')->orderByDesc('fecha')->limit(8)->get();

        return view('empleado.dashboard', compact(
            'pedidosPendientes', 'ventasHoy', 'ingredientesBajos', 'ultimosPedidos'
        ));
    }

    // RI-030: listado de pedidos para que el empleado los procese.
    public function pedidos()
    {
        $pedidos = Order::with('usuario', 'items.producto')->orderByDesc('fecha')->paginate(20);

        return view('empleado.pedidos', compact('pedidos'));
    }

    public function actualizarEstadoPedido(Request $request, Order $pedido)
    {
        $request->validate([
            'estado' => 'required|in:en_proceso,preparando,listo,entregado,cancelado',
        ]);

        $pedido->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado del pedido #'.$pedido->id.' actualizado.');
    }

    // RF-029 · RI-029 · RN-017: registrar una venta manual (mostrador/teléfono).
    public function crearVenta()
    {
        $productos = Product::activos()->orderBy('nombre')->get();
        $clientes = Usuario::orderBy('nombre')->get(['id', 'nombre']);

        return view('empleado.ventas.create', compact('productos', 'clientes'));
    }

    public function registrarVenta(Request $request)
    {
        $request->validate([
            'id_usuario' => 'nullable|exists:usuarios,id',
            'forma_pago' => 'required|in:efectivo,tarjeta,transferencia,nequi,daviplata',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ], [
            'productos.required' => 'Agrega al menos un producto a la venta.',
        ]);

        $pedido = DB::transaction(function () use ($request) {
            $total = 0;
            $lineas = [];

            foreach ($request->productos as $linea) {
                $producto = Product::findOrFail($linea['id']);
                $subtotal = $producto->precio * $linea['cantidad'];
                $total += $subtotal;
                $lineas[] = [
                    'id_producto' => $producto->id,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                ];
            }

            $pedido = Order::create([
                'id_usuario' => $request->id_usuario,
                'total' => $total,
                'estado' => 'en_proceso',
                'forma_pago' => $request->forma_pago,
                'fecha' => now(),
            ]);

            foreach ($lineas as $linea) {
                OrderItem::create(array_merge(['id_pedido' => $pedido->id], $linea));
            }

            return $pedido;
        });

        return redirect()->route('empleado.ventas.create')
            ->with('success', 'Venta #'.$pedido->id.' registrada correctamente.');
    }

    // RF-030 · RI-030: historial de ventas realizadas.
    public function historial(Request $request)
    {
        $query = Order::with('usuario', 'items.producto')->orderByDesc('fecha');

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        $pedidos = $query->paginate(20)->withQueryString();

        return view('empleado.historial', compact('pedidos'));
    }

    // RF-032: consultar ingredientes disponibles (solo lectura para empleados).
    public function inventario()
    {
        $ingredientes = Ingrediente::with('proveedores')->orderBy('nombre')->get();

        return view('empleado.inventario', compact('ingredientes'));
    }
}
