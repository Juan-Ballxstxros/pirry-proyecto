<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session('carrito', []);
    }

    private function saveCart(array $cart): void
    {
        session(['carrito' => $cart]);
    }

    public function show()
    {
        $cart = $this->getCart();
        $items = [];
        $total = 0;

        foreach ($cart as $item) {
            $subtotal = $item['precio'] * $item['cantidad'];
            $items[] = array_merge($item, ['subtotal' => $subtotal]);
            $total += $subtotal;
        }

        return response()->json([
            'items' => $items,
            'total' => $total,
            'count' => array_sum(array_column($cart, 'cantidad')),
            // Estado de sesión real (Auth::check del sistema existente):
            // lo usa el botón de WhatsApp para decidir si muestra el
            // modal de inicio de sesión. No altera la lógica del carrito.
            'logueado' => Auth::check(),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:productos,id',
        ]);

        $producto = Product::activos()->where('disponible', true)->findOrFail($request->id);
        $cart = $this->getCart();
        $found = false;

        foreach ($cart as &$item) {
            if ($item['id'] == $producto->id) {
                $item['cantidad']++;
                $found = true;
                break;
            }
        }
        unset($item);

        if (! $found) {
            $cart[] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => (float) $producto->precio,
                'imagen' => $producto->imagen,
                'cantidad' => 1,
            ];
        }

        $this->saveCart($cart);

        return response()->json([
            'exito' => 'Producto agregado al carrito',
            'count' => array_sum(array_column($cart, 'cantidad')),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();

        foreach ($cart as &$item) {
            if ($item['id'] == $request->id) {
                $item['cantidad'] = $request->cantidad;
                break;
            }
        }
        unset($item);

        $this->saveCart($cart);

        return $this->show();
    }

    public function remove(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $cart = array_values(array_filter(
            $this->getCart(),
            fn ($item) => $item['id'] != $request->id
        ));

        $this->saveCart($cart);

        return $this->show();
    }

    public function confirm(Request $request)
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return response()->json(['error' => 'El carrito está vacío'], 400);
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        $pedido = Order::create([
            'id_usuario' => Auth::id(),
            'total' => $total,
            'estado' => 'en_proceso',
            'fecha' => now(),
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'id_pedido' => $pedido->id,
                'id_producto' => $item['id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'subtotal' => $item['precio'] * $item['cantidad'],
            ]);
        }

        session()->forget('carrito');

        return response()->json([
            'exito' => 'Pedido confirmado',
            'pedido_id' => $pedido->id,
            'total' => $total,
        ]);
    }
}
