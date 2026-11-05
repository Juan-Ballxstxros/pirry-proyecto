<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Historial de cambios de estado de un pedido (seguimiento).
 * Cada cambio hecho por el administrador/empleado queda registrado con
 * fecha y el usuario responsable, para que el cliente pueda consultarlo.
 */
class PedidoHistorial extends Model
{
    protected $table = 'historial_pedidos';

    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'estado_anterior',
        'estado_nuevo',
        'id_usuario',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_pedido');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
