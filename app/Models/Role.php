<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id_roles';

    public function getRouteKeyName(): string
    {
        return 'id_roles';
    }

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'estado'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_rol', 'id_roles');
    }

    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'rol_permiso',
            'id_roles',
            'id_permisos',
            'id_roles',
            'id_permisos'
        );
    }
}
