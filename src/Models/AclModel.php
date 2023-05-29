<?php

declare(Strict_types=1);

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;

class AclModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_models';


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
        'name',
    ];
}
