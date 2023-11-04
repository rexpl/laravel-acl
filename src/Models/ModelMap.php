<?php

declare(Strict_types=1);

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
use Rexpl\LaravelAcl\Support\AclModel;

class ModelMap extends Model
{
    use AclModel;

    /**
     * The table associated with the model excluding the suffix.
     *
     * @var string
     */
    protected string $tableName = '_model_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['id'];
}
