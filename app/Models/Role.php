<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Role
 *
 * @property $id
 * @property $name
 *
 * @property RolePermission[] $rolePermissions
 * @property User[] $users
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Role extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];


    public $timestamps = false;


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rolePermissions()
    {
        return $this->hasMany(\App\Models\RolePermission::class, 'id', 'role_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'id', 'role_id');
    }

    public function permissions(): BelongsToMany
    {
        /*
         * La sintaxis completa es:
         *
         * return $this->belongsToMany(
         * ModeloRelacionado::class,
         * 'nombre_de_la_tabla_pivote',
         * 'columna_clave_foranea_de_este_modelo', // (Role)
         * 'columna_clave_foranea_del_otro_modelo' // (Permission)
         * );
         */

        return $this->belongsToMany(
            Permission::class,
            'role_permissions', // 1. Nombre de tu tabla pivote (del error anterior)
            'role_id',          // 2. Tu columna para el ID del rol (¡Confirmado!)
            'permission_id'     // 3. Tu columna para el ID del permiso (¡Confirmado!)
        );
    }
    
}
