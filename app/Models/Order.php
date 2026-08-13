<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'pedidos';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'total',
        'estado',
        'fecha',
    ];

    protected $casts = [
        'total' => 'float',
        'fecha' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_pedido');
    }
}
