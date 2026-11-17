<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'pedidos';

    public $timestamps = false;

    /**
     * Estados del seguimiento de pedidos.
     * FLUJO: la línea de tiempo que ve el cliente (sin "cancelado", que es
     * un estado especial fuera del avance normal).
     * ESTADOS_ACTIVOS: pedidos que aún se están atendiendo en cocina/entrega.
     */
    public const ESTADOS = ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado', 'cancelado'];
    public const FLUJO = ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado'];
    public const ESTADOS_ACTIVOS = ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino'];

    protected $fillable = [
        'codigo',
        'id_usuario',
        'nombre_cliente',
        'total',
        'estado',
        'forma_pago',
        'fecha',
    ];

    protected $casts = [
        'total' => 'float',
        'fecha' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Código legible del pedido (ej. PIR-00012) generado con el id real.
        static::created(function (Order $pedido) {
            if (! $pedido->codigo) {
                $pedido->codigo = 'PIR-'.str_pad((string) $pedido->id, 5, '0', STR_PAD_LEFT);
                $pedido->save();
            }
        });
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_pedido');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(PedidoHistorial::class, 'id_pedido')->orderBy('fecha')->orderBy('id');
    }

    /**
     * Nombre mostrado del cliente (ventas manuales o pedido de usuario).
     */
    public function nombreCliente(): string
    {
        return $this->nombre_cliente ?? $this->usuario?->nombre ?? 'Invitado';
    }

    /**
     * Registra un cambio de estado en el historial del pedido.
     */
    public function registrarCambioEstado(string $nuevoEstado, ?int $idUsuario = null, ?string $anterior = null): void
    {
        PedidoHistorial::create([
            'id_pedido' => $this->id,
            'estado_anterior' => $anterior ?? $this->estado,
            'estado_nuevo' => $nuevoEstado,
            'id_usuario' => $idUsuario,
            'fecha' => now(),
        ]);
    }
}

