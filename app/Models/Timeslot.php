<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Timeslot
 *
 * @property $id
 * @property $day
 * @property $start
 * @property $end
 *
 * @property ClassAssignment[] $classAssignments
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Timeslot extends Model
{
    
    protected $perPage = 20;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['day', 'start', 'end'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classAssignments()
    {
        return $this->hasMany(\App\Models\ClassAssignment::class, 'id', 'timeslot_id');
    }
    
}
