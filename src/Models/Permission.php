<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_permissions';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Get all the groups permissions by id.
     */
    public function groups()
    {
        return $this->hasMany(GroupPermission::class, 'permission_id');
    }
}