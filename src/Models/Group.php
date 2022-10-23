<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
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
     */
    public function users()
    {
        return $this->hasMany(GroupUser::class, 'group_id');
    }


    /**
     * Get all the groups permissions by id.
     */
    public function permissions()
    {
        return $this->hasMany(GroupPermission::class, 'group_id');
    }


    /**
     * get all the groups parent groups.
     */
    public function parents()
    {
        return $this->hasMany(ParentGroup::class, 'child_id');
    }


    /**
     * get all the groups parent groups.
     */
    public function childrens()
    {
        return $this->hasMany(ParentGroup::class, 'parent_id');
    }
}