<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('categoria')
            ->activos()
            ->where('disponible', true);

        if ($request->filled('categoria')) {
            $query->whereHas('categoria', fn ($q) => $q->where('nombre', $request->categoria));
        }

        if ($request->filled('busqueda')) {
            $query->where('nombre', 'like', '%'.$request->busqueda.'%');
        }

        $productos = $query->orderBy('id_categoria')->orderBy('nombre')->get();

        $data = $productos->map(function ($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'descripcion' => $p->descripcion,
                'precio' => (float) $p->precio,
                'imagen' => $p->imagen,
                'id_categoria' => $p->id_categoria,
                'categoria' => $p->categoria?->nombre,
                'disponible' => (bool) $p->disponible,
            ];
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data);
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        if (! Auth::user()?->isAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'id_categoria' => 'required|integer|exists:categorias,id',
            'disponible' => 'boolean',
        ]);

        $producto = Product::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? '',
            'precio' => $validated['precio'] ?? 0,
            'id_categoria' => $validated['id_categoria'],
            'disponible' => $validated['disponible'] ?? true,
            'estado' => 'activo',
        ]);

        return response()->json(['exito' => 'Producto creado', 'id' => $producto->id]);
    }

    public function update(Request $request, Product $producto)
    {
        if (! Auth::user()?->isAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'id_categoria' => 'required|integer|exists:categorias,id',
            'disponible' => 'boolean',
        ]);

        $producto->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? '',
            'precio' => $validated['precio'] ?? 0,
            'id_categoria' => $validated['id_categoria'],
            'disponible' => $request->boolean('disponible'),
        ]);

        return response()->json(['exito' => 'Producto actualizado']);
    }

    public function destroy(Product $producto)
    {
        if (! Auth::user()?->isAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $enProceso = $producto->detallePedidos()
            ->whereHas('pedido', fn ($q) => $q->where('estado', 'en_proceso'))
            ->exists();

        if ($enProceso) {
            return response()->json(['error' => 'No se puede eliminar: producto asociado a pedido en proceso']);
        }

        $producto->update([
            'estado' => 'inactivo',
            'fecha_eliminacion' => now(),
            'eliminado_por' => Auth::id(),
        ]);

        return response()->json(['exito' => 'Producto desactivado (trazabilidad conservada)']);
    }

    public function categorias()
    {
        return Category::orderBy('nombre')->get(['id', 'nombre']);
    }
}
