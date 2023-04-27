<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests\Policies;

use Rexpl\LaravelAcl\Policy;

class TextPolicy extends Policy
{
    /**
     * Acronym for resource.
     * 
     * @var string
     */
    protected string $acronym = 'test';


    /**
     * Required permissions.
     * 
     * @var array<string,string>
     */
    protected array $permissions = [
        'read' => 'test:read',
        'write' => 'test:write',
        'delete' => 'test:delete',
    ];
}