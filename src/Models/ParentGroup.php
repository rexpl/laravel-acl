<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentGroup extends Model
{
    // use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_parent_groups';
    

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Get the related group.
     */
    public function child()
    {
        return $this->belongsTo(Group::class, 'id', 'child_id');
    }


    /**
     * Get the related group.
     */
    public function parent()
    {
        return $this->belongsTo(Group::class, 'id', 'parent_id');
    }
}