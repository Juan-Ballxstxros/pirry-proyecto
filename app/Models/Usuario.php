<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    public $timestamps = false;
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'documento',
        'correo',
        'usuario',
        'contrasena',
        'id_rol',
        'estado',
        'reset_token',
        'token_expira',
    ];

    protected $hidden = [
        'contrasena',
        'reset_token',
    ];

    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_roles');
    }

    public function isAdmin(): bool
    {
        return $this->rol && strtolower($this->rol->nombre) === 'administrador';
    }

    public function isEmpleado(): bool
    {
        return $this->rol && strtolower($this->rol->nombre) === 'empleado';
    }

    /**
     * Admin o empleado: cualquiera de los dos puede entrar al panel interno
     * (el panel de administrador conserva secciones exclusivas de admin).
     */
    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isEmpleado();
    }

    public function isActive(): bool
    {
        return strtolower($this->estado) === 'activo';
    }
}

