<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompraIngrediente;
use App\Models\Ingrediente;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraIngredienteController extends Controller
{
    // RF-033 · RI-033: historial de compras de insumos.
    public function index()
    {
        $compras = CompraIngrediente::with('ingrediente', 'proveedor')
            ->orderByDesc('fecha')
            ->limit(200)
            ->get();

        return view('admin.compras.index', compact('compras'));
    }

    public function create()
    {
        $ingredientes = Ingrediente::where('estado', 'activo')->orderBy('nombre')->get();
        $proveedores = Proveedor::where('estado', 'activo')->orderBy('nombre')->get();

        return view('admin.compras.create', compact('ingredientes', 'proveedores'));
    }

    // RF-031 · RI-031 · RN-019: toda compra queda asociada a un proveedor existente,
    // cantidades y precio unitario deben ser mayores a cero, y la compra
    // incrementa automáticamente el stock del ingrediente.
    public function store(Request $request)
    {
        $request->validate([
            'id_ingrediente' => 'required|exists:ingredientes,id',
            'id_proveedor' => 'required|exists:proveedores,id',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $total = $request->cantidad * $request->precio_unitario;

            CompraIngrediente::create([
                'id_ingrediente' => $request->id_ingrediente,
                'id_proveedor' => $request->id_proveedor,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'total' => $total,
                'fecha' => now(),
            ]);

            $ingrediente = Ingrediente::findOrFail($request->id_ingrediente);
            $ingrediente->increment('stock_actual', $request->cantidad);

            // Deja constancia de que este proveedor sí surte este ingrediente.
            $ingrediente->proveedores()->syncWithoutDetaching([
                $request->id_proveedor => [
                    'disponible' => true,
                    'precio_referencia' => $request->precio_unitario,
                    'fecha_actualizacion' => now(),
                ],
            ]);
        });

        return redirect()->route('admin.compras.index')
            ->with('success', 'Compra registrada y stock actualizado.');
    }
}
