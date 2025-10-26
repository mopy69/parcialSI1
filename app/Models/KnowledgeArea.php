<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class KnowledgeArea
 *
 * @property $id
 * @property $name
 *
 * @property KnowledgeAreaUser[] $knowledgeAreaUsers
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class KnowledgeArea extends Model
{
    
    protected $perPage = 20;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function knowledgeAreaUsers()
    {
        return $this->hasMany(\App\Models\KnowledgeAreaUser::class, 'id', 'knowledge_areas_id');
    }
    
}
