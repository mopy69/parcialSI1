<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CourseOffering
 *
 * @property $id
 * @property $term_id
 * @property $subject_id
 * @property $group_id
 *
 * @property Group $group
 * @property Subject $subject
 * @property Term $term
 * @property ClassAssignment[] $classAssignments
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CourseOffering extends Model
{
    
    protected $perPage = 20;

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['term_id', 'subject_id', 'group_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class, 'group_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject()
    {
        return $this->belongsTo(\App\Models\Subject::class, 'subject_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(\App\Models\Term::class, 'term_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classAssignments()
    {
        return $this->hasMany(\App\Models\ClassAssignment::class, 'id', 'course_offering_id');
    }
    
}
