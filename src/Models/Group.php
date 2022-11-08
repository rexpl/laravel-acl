<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    // use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_groups';
    

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Get all the groups users.
     * 
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(GroupUser::class, 'group_id');
    }


    /**
     * Get all the groups permissions by id.
     * 
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(GroupPermission::class, 'group_id');
    }


    /**
     * Get all the groups parent groups.
     * 
     * @return HasMany
     */
    public function parents(): HasMany
    {
        return $this->hasMany(ParentGroup::class, 'child_id');
    }


    /**
     * Get all the groups parent groups.
     * 
     * @return HasMany
     */
    public function childrens(): HasMany
    {
        return $this->hasMany(ParentGroup::class, 'parent_id');
    }
}