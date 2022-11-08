<?php

namespace Rexpl\LaravelAcl\Tests;

use Rexpl\LaravelAcl\Policy;

class TestPolicy extends Policy
{
    /**
     * Acronym.
     *
     * @var string
     */
    protected $acronym = 'test';


    /**
     * Needed permissions
     *
     * @var array
     */
    protected $permissions = [
        'read' => 'test:read',
        'write' => 'test:write',
        'delete' => 'test:delete',
    ];
}