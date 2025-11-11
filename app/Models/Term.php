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
     * Scope para obtener solo gestiones activas
     */
    public function scopeActive($query)
    {
        return $query->where('asset', true);
    }

    /**
     * Scope para obtener la gestión actual (activa y dentro del rango de fechas)
     */
    public function scopeCurrent($query)
    {
        return $query->where('asset', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Verifica si la gestión está activa según sus fechas
     */
    public function shouldBeActive(): bool
    {
        $today = \Carbon\Carbon::today();
        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate = \Carbon\Carbon::parse($this->end_date);
        
        return $today->greaterThanOrEqualTo($startDate) && $today->lessThanOrEqualTo($endDate);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function courseOfferings()
    {
        return $this->hasMany(\App\Models\CourseOffering::class, 'id', 'term_id');
    }
    
}
