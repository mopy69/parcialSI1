<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use app\Models\User as user;

/**
 * Class ClassAssignment
 *
 * @property $id
 * @property $coordinador_id
 * @property $docente_id
 * @property $timeslot_id
 * @property $course_offering_id
 * @property $classroom_id
 *
 * @property Classroom $classroom
 * @property User $user
 * @property CourseOffering $courseOffering
 * @property User $user
 * @property Timeslot $timeslot
 * @property TeacherAttendance[] $teacherAttendances
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ClassAssignment extends Model
{
    
    protected $perPage = 20;

    public $timestamps = false;

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['coordinador_id', 'docente_id', 'timeslot_id', 'course_offering_id', 'classroom_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classroom()
    {
        return $this->belongsTo(\App\Models\Classroom::class, 'classroom_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCoordinador()
    {
        return $this->belongsTo(\App\Models\User::class, 'coordinador_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseOffering()
    {
        return $this->belongsTo(\App\Models\CourseOffering::class, 'course_offering_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userDocente()
    {
        return $this->belongsTo(\App\Models\User::class, 'docente_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function timeslot()
    {
        return $this->belongsTo(\App\Models\Timeslot::class, 'timeslot_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teacherAttendances()
    {
        return $this->hasMany(\App\Models\TeacherAttendance::class, 'id', 'class_assignment_id');
    }
    
}
