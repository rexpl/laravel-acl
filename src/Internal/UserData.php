<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Internal;

class UserData
{
    /**
     * @param array<int> The id's to all the groups to wich the user belongs and there child groups.
     * @param array<string> The user permissions. All these permissions are directly attached to the user or inherited by groups and there parents.
     * @param array<\Rexpl\LaravelAcl\Internal\StdAclRow> $groupID The user standard acl.
     * 
     * @return void
     */
    public function __construct(
        public array $groups,
        public array $permissions,
        public array $stdAcl
    ) {}
}