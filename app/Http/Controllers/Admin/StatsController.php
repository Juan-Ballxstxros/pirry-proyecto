<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    /**
     * Vista con los gráficos completos de estadísticas.
     */
    public function view()
    {
        return view('admin.estadisticas.index');
    }

    /**
     * API interna (JSON) con las estadísticas del panel de administrador.
     * Consumida por fetch() desde el dashboard y la vista de estadísticas.
     *
     * GET /admin/api/estadisticas?dias=7|30|90
     */
    public function index(Request $request): JsonResponse
    {
        $dias = (int) $request->query('dias', 30);
        $dias = in_array($dias, [7, 30, 90], true) ? $dias : 30;

        $desde = Carbon::now()->subDays($dias - 1)->startOfDay();

        return response()->json([
            'rango_dias' => $dias,
            'resumen' => $this->resumen($desde),
            'ventas_por_dia' => $this->ventasPorDia($desde, $dias),
            'pedidos_por_estado' => $this->pedidosPorEstado($desde),
            'productos_top' => $this->productosTop($desde),
            'usuarios_por_rol' => $this->usuariosPorRol(),
            'horarios_pico' => $this->horariosPico($desde),
        ]);
    }

    /**
     * RF-028 · RI-028 · RN-016: informe de ventas por franja horaria.
     * Agrupa por hora del día (0-23) para identificar los horarios de mayor
     * actividad. Los "horarios pico" son configurables: por defecto se
     * consideran pico las 3 horas con mayor facturación del periodo.
     */
    private function horariosPico(Carbon $desde, int $picosDestacados = 3): array
    {
        $filas = Order::where('fecha', '>=', $desde)
            ->selectRaw('HOUR(fecha) as hora, COUNT(*) as pedidos, SUM(total) as total')
            ->groupBy('hora')
            ->get()
            ->keyBy('hora');

        $serie = [];
        for ($hora = 0; $hora < 24; $hora++) {
            $fila = $filas->get($hora);
            $serie[] = [
                'hora' => $hora,
                'etiqueta' => sprintf('%02d:00', $hora),
                'pedidos' => $fila ? (int) $fila->pedidos : 0,
                'total' => $fila ? round((float) $fila->total, 2) : 0.0,
            ];
        }

        $picos = collect($serie)
            ->sortByDesc('total')
            ->take($picosDestacados)
            ->pluck('etiqueta')
            ->values()
            ->all();

        return [
            'serie' => $serie,
            'horarios_destacados' => $picos,
        ];
    }

    private function resumen(Carbon $desde): array
    {
        $pedidosQuery = Order::where('fecha', '>=', $desde);
        $pedidosTotales = (clone $pedidosQuery)->count();
        $ingresosTotales = (float) (clone $pedidosQuery)->sum('total');

        $hoy = Carbon::today();

        return [
            'ingresos_totales' => round($ingresosTotales, 2),
            'pedidos_totales' => $pedidosTotales,
            'ticket_promedio' => $pedidosTotales > 0 ? round($ingresosTotales / $pedidosTotales, 2) : 0.0,
            'pedidos_hoy' => Order::whereDate('fecha', $hoy)->count(),
            'ingresos_hoy' => round((float) Order::whereDate('fecha', $hoy)->sum('total'), 2),
            'usuarios_activos' => Usuario::whereRaw("LOWER(estado) = 'activo'")->count(),
            'productos_activos' => Product::whereRaw("LOWER(estado) = 'activo'")->count(),
        ];
    }

    /**
     * Serie diaria continua (rellena los días sin ventas con 0 para que el gráfico no tenga huecos).
     */
    private function ventasPorDia(Carbon $desde, int $dias): array
    {
        $filas = Order::where('fecha', '>=', $desde)
            ->selectRaw('DATE(fecha) as dia, COUNT(*) as pedidos, SUM(total) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $serie = [];
        for ($i = 0; $i < $dias; $i++) {
            $fecha = Carbon::now()->subDays($dias - 1 - $i)->format('Y-m-d');
            $fila = $filas->get($fecha);

            $serie[] = [
                'fecha' => $fecha,
                'pedidos' => $fila ? (int) $fila->pedidos : 0,
                'total' => $fila ? round((float) $fila->total, 2) : 0.0,
            ];
        }

        return $serie;
    }

    private function pedidosPorEstado(Carbon $desde): array
    {
        return Order::where('fecha', '>=', $desde)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($fila) => [
                'estado' => ucfirst(str_replace('_', ' ', $fila->estado ?? 'sin_estado')),
                'total' => (int) $fila->total,
            ])
            ->values()
            ->all();
    }

    private function productosTop(Carbon $desde, int $limite = 5): array
    {
        return OrderItem::query()
            ->join('pedidos', 'detalle_pedido.id_pedido', '=', 'pedidos.id')
            ->join('productos', 'detalle_pedido.id_producto', '=', 'productos.id')
            ->where('pedidos.fecha', '>=', $desde)
            ->selectRaw('productos.nombre as nombre, SUM(detalle_pedido.cantidad) as cantidad, SUM(detalle_pedido.subtotal) as ingresos')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('cantidad')
            ->limit($limite)
            ->get()
            ->map(fn ($fila) => [
                'nombre' => $fila->nombre,
                'cantidad' => (int) $fila->cantidad,
                'ingresos' => round((float) $fila->ingresos, 2),
            ])
            ->values()
            ->all();
    }

    private function usuariosPorRol(): array
    {
        return Usuario::query()
            ->join('roles', 'usuarios.id_rol', '=', 'roles.id_roles')
            ->selectRaw('roles.nombre as rol, COUNT(*) as total')
            ->groupBy('roles.id_roles', 'roles.nombre')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($fila) => ['rol' => $fila->rol, 'total' => (int) $fila->total])
            ->values()
            ->all();
    }
}
