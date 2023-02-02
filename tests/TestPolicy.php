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
    protected string $acronym = 'test';


    /**
     * Needed permissions
     *
     * @var array
     */
    protected array $permissions = [
        'read' => 'test:read',
        'write' => 'test:write',
        'delete' => 'test:delete',
    ];
}