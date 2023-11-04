<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rexpl\LaravelAcl\Support\AclModel;

class Permission extends Model
{
    use AclModel;

    /**
     * The table associated with the model excluding the suffix.
     *
     * @var string
     */
    protected string $tableName = '_permissions';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['id'];


    /**
     * Get all the groups permissions by id.
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            Group::class,
            $this->tablePrefix. '_group_permissions',
            'permission_id',
            'group_id'
        );
    }
}
