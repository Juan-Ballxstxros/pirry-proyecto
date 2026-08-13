<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'productos';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'imagen',
        'id_categoria',
        'disponible',
        'estado',
        'fecha_eliminacion',
        'eliminado_por',
    ];

    protected $casts = [
        'precio' => 'float',
        'disponible' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_categoria');
    }

    public function detallePedidos(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_producto');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
