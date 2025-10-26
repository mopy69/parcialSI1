<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 * @property $ip_address
 * @property $action
 * @property $state
 * @property $details
 * @property $user_id
 *
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Log extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['ip_address', 'action', 'state', 'details', 'user_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
    
}
