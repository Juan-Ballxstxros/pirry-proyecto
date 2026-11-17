<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingrediente;
use App\Models\Product;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class IngredienteController extends Controller
{
    public function index()
    {
        $ingredientes = Ingrediente::withCount('proveedores', 'productos')->orderBy('nombre')->get();

        return view('admin.ingredientes.index', compact('ingredientes'));
    }

    public function create()
    {
        return view('admin.ingredientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'unidad_medida' => 'required|string|max:20',
            'stock_minimo' => 'required|numeric|min:0',
        ]);

        Ingrediente::create([
            'nombre' => $request->nombre,
            'unidad_medida' => $request->unidad_medida,
            'stock_actual' => 0,
            'stock_minimo' => $request->stock_minimo,
            'estado' => 'activo',
        ]);

        return redirect()->route('admin.ingredientes.index')
            ->with('success', 'Ingrediente registrado correctamente.');
    }

    // RI-032: aquí se ve/gestiona la relación con proveedores (disponibilidad,
    // precio de referencia) y con productos (receta: qué productos lo usan).
    public function edit(Ingrediente $ingrediente)
    {
        $ingrediente->load('proveedores', 'productos');
        $proveedores = Proveedor::where('estado', 'activo')->orderBy('nombre')->get();
        $productos = Product::activos()->orderBy('nombre')->get();

        return view('admin.ingredientes.edit', compact('ingrediente', 'proveedores', 'productos'));
    }

    public function update(Request $request, Ingrediente $ingrediente)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'unidad_medida' => 'required|string|max:20',
            'stock_minimo' => 'required|numeric|min:0',
        ]);

        $ingrediente->update($request->only('nombre', 'unidad_medida', 'stock_minimo'));

        return redirect()->route('admin.ingredientes.edit', $ingrediente)
            ->with('success', 'Ingrediente actualizado correctamente.');
    }

    public function toggleEstado(Ingrediente $ingrediente)
    {
        $ingrediente->estado = strtolower($ingrediente->estado) === 'activo' ? 'inactivo' : 'activo';
        $ingrediente->save();

        return back()->with('success', 'Estado del ingrediente actualizado.');
    }

    // Vincula/actualiza un proveedor para este ingrediente (RI-032).
    public function asociarProveedor(Request $request, Ingrediente $ingrediente)
    {
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id',
            'precio_referencia' => 'nullable|numeric|min:0',
            'disponible' => 'nullable|boolean',
        ]);

        $ingrediente->proveedores()->syncWithoutDetaching([
            $request->id_proveedor => [
                'disponible' => $request->boolean('disponible', true),
                'precio_referencia' => $request->precio_referencia,
                'fecha_actualizacion' => now(),
            ],
        ]);

        return back()->with('success', 'Proveedor asociado al ingrediente.');
    }

    public function quitarProveedor(Ingrediente $ingrediente, Proveedor $proveedor)
    {
        $ingrediente->proveedores()->detach($proveedor->id);

        return back()->with('success', 'Proveedor desvinculado del ingrediente.');
    }

    // Define la receta: cuánta cantidad de este ingrediente usa un producto.
    public function asociarProducto(Request $request, Ingrediente $ingrediente)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'cantidad_requerida' => 'required|numeric|min:0.01',
        ]);

        $ingrediente->productos()->syncWithoutDetaching([
            $request->id_producto => ['cantidad_requerida' => $request->cantidad_requerida],
        ]);

        return back()->with('success', 'Producto vinculado al ingrediente.');
    }

    public function quitarProducto(Ingrediente $ingrediente, Product $producto)
    {
        $ingrediente->productos()->detach($producto->id);

        return back()->with('success', 'Producto desvinculado del ingrediente.');
    }
}
