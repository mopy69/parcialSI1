<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TeacherAttendance
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 * @property $type
 * @property $state
 * @property $class_assignment_id
 *
 * @property ClassAssignment $classAssignment
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class TeacherAttendance extends Model
{
    
    protected $perPage = 20;
    
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['type', 'state', 'class_assignment_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classAssignment()
    {
        return $this->belongsTo(\App\Models\ClassAssignment::class, 'class_assignment_id', 'id');
    }
    
}
