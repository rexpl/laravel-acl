<?php

namespace Rexpl\LaravelAcl\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Rexpl\LaravelAcl\Support\AclQuery;

class TestModel extends Model
{
    use AclQuery;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tests';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [
        'id'
    ];
}