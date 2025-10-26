<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class KnowledgeAreaUser
 *
 * @property $user_id
 * @property $knowledge_areas_id
 *
 * @property KnowledgeArea $knowledgeArea
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class KnowledgeAreaUser extends Model
{
    
    protected $perPage = 20;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'knowledge_areas_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function knowledgeArea()
    {
        return $this->belongsTo(\App\Models\KnowledgeArea::class, 'knowledge_areas_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
    
}
