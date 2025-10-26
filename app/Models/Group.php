<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 *
 * @property $id
 * @property $name
 * @property $semester
 *
 * @property CourseOffering[] $courseOfferings
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Group extends Model
{
    
    protected $perPage = 20;
    
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'semester'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseOfferings()
    {
        return $this->hasMany(\App\Models\CourseOffering::class,  'group_id','id');
    }
    
}
