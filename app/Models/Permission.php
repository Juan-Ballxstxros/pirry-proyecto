<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'permisos';

    protected $primaryKey = 'id_permisos';

    public function getRouteKeyName(): string
    {
        return 'id_permisos';
    }

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'rol_permiso',
            'id_permisos',
            'id_roles',
            'id_permisos',
            'id_roles'
        );
    }
}
