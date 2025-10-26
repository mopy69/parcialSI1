<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Classroom
 *
 * @property $id
 * @property $capacity
 * @property $nro
 * @property $type
 *
 * @property ClassAssignment[] $classAssignments
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Classroom extends Model
{
    
    protected $perPage = 20;

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['capacity', 'nro', 'type'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classAssignments()
    {
        return $this->hasMany(\App\Models\ClassAssignment::class, 'classroom_id','id');
    }
    
}
