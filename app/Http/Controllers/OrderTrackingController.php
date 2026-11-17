<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Seguimiento de pedidos para el CLIENTE ("Mis pedidos").
 *
 * Seguridad: todas las consultas se filtran SIEMPRE por Auth::id(). El
 * controlador nunca acepta un id de pedido externo, así que aunque alguien
 * manipule la URL no puede ver pedidos de otros usuarios. Cambiar el estado
 * NO existe aquí: eso solo lo hacen admin/empleado vía sus propios PATCH.
 */
class OrderTrackingController extends Controller
{
    /**
     * Vista "Mis pedidos" (el listado y la línea de tiempo los pinta
     * MisPedidos.vue con polling para ver los cambios en vivo).
     */
    public function index()
    {
        return view('pedidos.mis-pedidos');
    }

    /**
     * API JSON con TODOS los pedidos del usuario autenticado (con productos,
     * historial de estados y totales). Consumida por MisPedidos.vue.
     */
    public function api(): JsonResponse
    {
        $usuario = Auth::user();

        $pedidos = Order::with(['items.producto', 'historial.usuario'])
            ->where('id_usuario', $usuario->id)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Order $pedido) => $this->formatPedido($pedido))
            ->values();

        return response()->json([
            'pedidos' => $pedidos,
            'actualizado_en' => now()->format('d/m/Y H:i:s'),
        ]);
    }

    /**
     * Detalle JSON de UN pedido propio (usa route model binding con el id
     * pero la consulta fuerza el dueño: si no es suyo devuelve 404, nunca
     * revela pedidos ajenos).
     */
    public function detalle(Order $pedido): JsonResponse
    {
        if ((int) $pedido->id_usuario !== (int) Auth::id()) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        return response()->json(['pedido' => $this->formatPedido($pedido->load(['items.producto', 'historial.usuario']))]);
    }

    private function formatPedido(Order $pedido): array
    {
        return [
            'id' => $pedido->id,
            'codigo' => $pedido->codigo ?? 'PIR-'.str_pad((string) $pedido->id, 5, '0', STR_PAD_LEFT),
            'estado' => $pedido->estado,
            'total' => (float) $pedido->total,
            'forma_pago' => $pedido->forma_pago,
            'fecha' => $pedido->fecha?->format('d/m/Y H:i'),
            'productos' => $pedido->items->map(fn ($item) => [
                'nombre' => $item->producto?->nombre ?? 'Producto eliminado',
                'cantidad' => (int) $item->cantidad,
                'precio_unitario' => (float) $item->precio_unitario,
                'subtotal' => (float) $item->subtotal,
            ])->values(),
            'historial' => $pedido->historial->map(fn ($h) => [
                'estado_anterior' => $h->estado_anterior,
                'estado_nuevo' => $h->estado_nuevo,
                'fecha' => $h->fecha?->format('d/m/Y H:i'),
                'usuario' => $h->usuario?->nombre,
            ])->values(),
        ];
    }
}
