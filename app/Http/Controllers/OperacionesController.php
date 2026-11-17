<?php

namespace App\Http\Controllers;

use App\Models\Ingrediente;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Funciones operativas del día a día (procesar pedidos, registrar ventas
 * de mostrador, ver historial e inventario). Las usan tanto el panel de
 * empleados (/empleado) como el panel de administrador (/admin), cada
 * uno con su propio layout y permisos — el admin ya no necesita entrar
 * al panel de empleados para hacer estas tareas.
 */
class OperacionesController extends Controller
{
    /**
     * Detecta desde qué panel se llegó (por el nombre de la ruta) para
     * usar el layout correcto sin duplicar controladores ni vistas.
     */
    private function layout(): string
    {
        return request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.empleado';
    }

    public function dashboard()
    {
        $pedidosPendientes = Order::whereIn('estado', Order::ESTADOS_ACTIVOS)->count();
        $ventasHoy = Order::whereDate('fecha', Carbon::today())->count();
        $ingredientesBajos = Ingrediente::where('estado', 'activo')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->count();

        $ultimosPedidos = Order::with('usuario')->orderByDesc('fecha')->limit(8)->get();

        return view('operaciones.dashboard', [
            'layout' => $this->layout(),
        ] + compact('pedidosPendientes', 'ventasHoy', 'ingredientesBajos', 'ultimosPedidos'));
    }

    public function pedidos(Request $request)
    {
        // Búsqueda por código o nombre del cliente + filtro por estado (RF panel).
        $query = Order::with('usuario', 'items.producto', 'historial.usuario')->orderByDesc('fecha');

        if ($request->filled('q')) {
            $termino = trim($request->q);
            $query->where(function ($sub) use ($termino) {
                $sub->where('codigo', 'like', "%{$termino}%")
                    ->orWhere('nombre_cliente', 'like', "%{$termino}%")
                    ->orWhereHas('usuario', fn ($u) => $u->where('nombre', 'like', "%{$termino}%"));
            });
        }

        if ($request->filled('estado') && in_array($request->estado, Order::ESTADOS, true)) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->paginate(20)->withQueryString();

        return view('operaciones.pedidos', ['layout' => $this->layout()] + compact('pedidos'));
    }

    public function actualizarEstadoPedido(Request $request, Order $pedido)
    {
        // Validación en el SERVIDOR contra el catálogo de estados del seguimiento.
        $request->validate([
            'estado' => 'required|in:'.implode(',', Order::ESTADOS),
        ]);

        $estadoAnterior = $pedido->estado;

        if ($estadoAnterior !== $request->estado) {
            $pedido->update(['estado' => $request->estado]);

            // El cambio queda registrado en el historial con fecha y responsable.
            $pedido->registrarCambioEstado($request->estado, Auth::id(), $estadoAnterior);
        }

        return back()->with('success', 'Estado del pedido '.$pedido->codigo.' actualizado.');
    }

    public function crearVenta()
    {
        $productos = Product::activos()->orderBy('nombre')->get();
        $clientes = Usuario::orderBy('nombre')->get(['id', 'nombre']);

        return view('operaciones.ventas-crear', ['layout' => $this->layout()] + compact('productos', 'clientes'));
    }

    // RF-029 · RI-029 · RN-017: registrar una venta manual (mostrador/teléfono).
    public function registrarVenta(Request $request)
    {
        $request->validate([
            'id_usuario' => 'nullable|exists:usuarios,id',
            'nombre_cliente' => 'required|string|max:100',
            'forma_pago' => 'required|in:efectivo,tarjeta,transferencia,nequi,daviplata',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ], [
            'productos.required' => 'Agrega al menos un producto a la venta.',
            'nombre_cliente.required' => 'Indica a nombre de quién es el pedido.',
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
                'nombre_cliente' => $request->nombre_cliente,
                'total' => $total,
                'estado' => Order::ESTADOS[0], // pendiente
                'forma_pago' => $request->forma_pago,
                'fecha' => now(),
            ]);

            foreach ($lineas as $linea) {
                OrderItem::create(array_merge(['id_pedido' => $pedido->id], $linea));
            }

            // Primer registro del historial de seguimiento.
            $pedido->registrarCambioEstado($pedido->estado, Auth::id());

            return $pedido;
        });

        $ruta = request()->routeIs('admin.*') ? 'admin.ventas.create' : 'empleado.ventas.create';

        return redirect()->route($ruta)
            ->with('success', 'Venta #'.$pedido->id.' registrada a nombre de '.$pedido->nombre_cliente.'.');
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

        return view('operaciones.historial', ['layout' => $this->layout()] + compact('pedidos'));
    }

    // RF-032: consultar ingredientes disponibles (solo lectura).
    public function inventario()
    {
        $ingredientes = Ingrediente::with('proveedores')->orderBy('nombre')->get();

        return view('operaciones.inventario', ['layout' => $this->layout()] + compact('ingredientes'));
    }
}