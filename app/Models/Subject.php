<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Subject
 *
 * @property $id
 * @property $code
 * @property $name
 *
 * @property CourseOffering[] $courseOfferings
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Subject extends Model
{
    
    protected $perPage = 20;

    // Tu migración no tiene timestamps, así que esto es ¡Correcto!
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * (¡Esto está perfecto!)
     * @var array<int, string>
     */
    protected $fillable = ['code', 'name'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseOfferings()
    {
        // ¡CORREGIDO! Los argumentos de hasMany estaban invertidos.
        return $this->hasMany(\App\Models\CourseOffering::class, 'subject_id', 'id');
    }
    
}