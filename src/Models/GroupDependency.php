<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupDependency extends Model
{
    // use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_group_dependencies';
    

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array<string>
    */
    protected $fillable = [
        'ressource',
        'ressource_id',
        'group_id',
        'permission_level',
    ];


    /**
     * Get the related group.
     * 
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}