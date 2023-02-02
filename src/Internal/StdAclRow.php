<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Internal;

class StdAclRow
{
    /**
     * @param int $permissionLevel The permission level of this acl row.
     * @param int $groupID The group ID of who belongs this row.
     * 
     * @return void
     */
    public function __construct(
        public int $permissionLevel,
        public int $groupID 
    ) {}
}