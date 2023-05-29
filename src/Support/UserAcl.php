<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Support;

use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\User;

trait UserAcl
{
    /**
     * Returns the acl instance of the user.
     *
     * @return \Rexpl\LaravelAcl\User
     */
    public function acl(): User
    {
        return Acl::user($this);
    }
}
