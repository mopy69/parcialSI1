<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Term
 *
 * @property $id
 * @property $name
 * @property $start_date
 * @property $end_date
 * @property $asset
 *
 * @property CourseOffering[] $courseOfferings
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Term extends Model
{
    
    protected $perPage = 20;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'start_date', 'end_date', 'asset'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function courseOfferings()
    {
        return $this->hasMany(\App\Models\CourseOffering::class, 'id', 'term_id');
    }
    
}
