<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rexpl\LaravelAcl\Support\AclModel;

class Group extends Model
{
    use AclModel;

    /**
     * The table associated with the model excluding the suffix.
     *
     * @var string
     */
    protected string $tableName = '_groups';

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
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            $this->tablePrefix. '_group_permissions',
            'group_id',
            'permission_id'
        );
    }


    /**
     * Get all the groups parent groups.
     *
     * @return BelongsToMany
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            $this->tablePrefix. '_parent_groups',
            'child_id',
            'parent_id'
        );
    }


    /**
     * Get all the groups parent groups.
     *
     * @return BelongsToMany
     */
    public function childrens(): BelongsToMany
    {
        return $this->belongsToMany(self::class,
            $this->tablePrefix. '_parent_groups',
            'parent_id',
            'child_id'
        );
    }
}
