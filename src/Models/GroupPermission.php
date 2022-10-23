<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupPermission extends Model
{
    // use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_group_permissions';
    

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Get the related group.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }


    /**
     * Get the related permission.
     */
    public function permission()
    {
        return $this->belongsTo(Permisson::class, 'permission_id', 'id');
    }
}