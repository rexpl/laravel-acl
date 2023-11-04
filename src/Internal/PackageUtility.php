<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Internal;

use Rexpl\LaravelAcl\Acl;
use Rexpl\LaravelAcl\Group;

trait PackageUtility
{
    /**
     * Acl instance.
     *
     * @var \Rexpl\LaravelAcl\Acl
     */
    private static Acl $acl;


    /**
     * Returns an acl instance.
     *
     * @return \Rexpl\LaravelAcl\Acl
     */
    protected function acl(): Acl
    {
        return self::$acl ?? $this->makeNewAclInstance();
    }


    /**
     * Make new acl instance.
     *
     * @return \Rexpl\LaravelAcl\Acl
     */
    private function makeNewAclInstance(): Acl
    {
        return self::$acl = new Acl();
    }


    /**
     * Return a valid group.
     *
     * @param \Rexpl\LaravelAcl\Group|int|string $group
     *
     * @return \Rexpl\LaravelAcl\Group
     */
    protected function validGroup(Group|int|string $group): Group
    {
        if (is_scalar($group)) $group = $this->acl()->group($group);

        return $group;
    }
}
