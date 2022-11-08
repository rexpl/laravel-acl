<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class StdAcl extends Model
{
    // use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_user_std';


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
        'user_id',
        'group_id',
        'permission_level',
    ];
}