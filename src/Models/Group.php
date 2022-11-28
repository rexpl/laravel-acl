<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
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
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'acl_group_permissions', 'group_id', 'permission_id');
    }


    /**
     * Get all the groups parent groups.
     * 
     * @return BelongsToMany
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'acl_parent_groups', 'child_id', 'parent_id');
    }


    /**
     * Get all the groups parent groups.
     * 
     * @return BelongsToMany
     */
    public function childrens(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'acl_parent_groups', 'parent_id', 'child_id');
    }
}