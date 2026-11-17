<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'identificacion',
        'telefono',
        'direccion',
        'email',
        'estado',
    ];

    public function ingredientes(): BelongsToMany
    {
        return $this->belongsToMany(Ingrediente::class, 'proveedor_ingrediente', 'id_proveedor', 'id_ingrediente')
            ->withPivot(['disponible', 'precio_referencia', 'fecha_actualizacion']);
    }

    public function compras(): HasMany
    {
        return $this->hasMany(CompraIngrediente::class, 'id_proveedor');
    }

    public function isActive(): bool
    {
        return strtolower($this->estado) === 'activo';
    }
}
