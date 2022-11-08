<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * 
     * @return BelongsTo
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'child_id', 'id');
    }


    /**
     * Get the related group.
     * 
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_id', 'id');
    }
}