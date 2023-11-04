<?php

namespace Rexpl\LaravelAcl\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Rexpl\LaravelAcl\Support\AclModel;

class GroupUser extends Pivot
{
    use AclModel;

    protected static bool $isPivotTable = true;

    /**
     * The table associated with the model excluding the suffix.
     *
     * @var string
     */
    protected string $tableName = '_group_users';
}
