<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraIngrediente extends Model
{
    protected $table = 'compras_ingredientes';

    public $timestamps = false;

    protected $fillable = [
        'id_ingrediente',
        'id_proveedor',
        'cantidad',
        'precio_unitario',
        'total',
        'fecha',
    ];

    protected $casts = [
        'cantidad' => 'float',
        'precio_unitario' => 'float',
        'total' => 'float',
        'fecha' => 'datetime',
    ];

    public function ingrediente(): BelongsTo
    {
        return $this->belongsTo(Ingrediente::class, 'id_ingrediente');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
}
