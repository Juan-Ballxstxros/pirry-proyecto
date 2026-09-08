<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingrediente extends Model
{
    protected $table = 'ingredientes';

    protected $fillable = [
        'nombre',
        'unidad_medida',
        'stock_actual',
        'stock_minimo',
        'estado',
    ];

    protected $casts = [
        'stock_actual' => 'float',
        'stock_minimo' => 'float',
    ];

    public function proveedores(): BelongsToMany
    {
        return $this->belongsToMany(Proveedor::class, 'proveedor_ingrediente', 'id_ingrediente', 'id_proveedor')
            ->withPivot(['disponible', 'precio_referencia', 'fecha_actualizacion']);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'producto_ingrediente', 'id_ingrediente', 'id_producto')
            ->withPivot(['cantidad_requerida']);
    }

    public function compras(): HasMany
    {
        return $this->hasMany(CompraIngrediente::class, 'id_ingrediente');
    }

    public function stockBajo(): bool
    {
        return (float) $this->stock_actual <= (float) $this->stock_minimo;
    }
}
