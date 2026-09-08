<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            // Solo las imágenes subidas por el admin (archivos locales en
            // productos/) se exponen como URL. Las URLs de ejemplo antiguas
            // quedan guardadas en la BD pero NO se muestran.
            $imagenUrl = null;
            if ($p->imagen && str_starts_with($p->imagen, 'productos/')) {
                $imagenUrl = Storage::disk('public')->url($p->imagen);
            }

            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'descripcion' => $p->descripcion,
                'precio' => (float) $p->precio,
                'imagen' => $imagenUrl,
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
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'Formato no permitido. Usa JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no debe pesar más de 2 MB.',
        ]);

        // Subida de imagen: nombre aleatorio seguro en storage/app/public/productos
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Product::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? '',
            'precio' => $validated['precio'] ?? 0,
            'id_categoria' => $validated['id_categoria'],
            'disponible' => $validated['disponible'] ?? true,
            'estado' => 'activo',
            'imagen' => $rutaImagen,
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
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'Formato no permitido. Usa JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no debe pesar más de 2 MB.',
        ]);

        $datosActualizar = [
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? '',
            'precio' => $validated['precio'] ?? 0,
            'id_categoria' => $validated['id_categoria'],
            'disponible' => $request->boolean('disponible'),
        ];

        // Si se sube una imagen nueva, reemplaza a la anterior. El archivo
        // viejo se borra físicamente SOLO si era una subida local
        // (productos/...), nunca una URL externa.
        if ($request->hasFile('imagen')) {
            if ($producto->imagen && str_starts_with($producto->imagen, 'productos/')) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $datosActualizar['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($datosActualizar);

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

    /**
     * Elimina SOLO la imagen de un producto (el producto se conserva).
     */
    public function eliminarImagen(Product $producto)
    {
        if (! Auth::user()?->isAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if (! $producto->imagen || ! str_starts_with($producto->imagen, 'productos/')) {
            return response()->json(['error' => 'El producto no tiene una imagen subida'], 400);
        }

        Storage::disk('public')->delete($producto->imagen);
        $producto->update(['imagen' => null]);

        return response()->json(['exito' => 'Imagen eliminada']);
    }

    public function categorias()
    {
        return Category::orderBy('nombre')->get(['id', 'nombre']);
    }
}
