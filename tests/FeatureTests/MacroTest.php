<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests\FeatureTests;

use BadMethodCallException;
use Rexpl\LaravelAcl\Facades\Acl;
use Rexpl\LaravelAcl\Tests\test_models\User;
use Rexpl\LaravelAcl\Tests\TestCase;

class MacroTest extends TestCase
{
    /**
     * Test the macro functionnality.
     *
     * @return void
     */
    public function test_macro(): void
    {
        $userModel = User::create([
            'name' => 'user:test_user_std_acl'
        ]);
        $user = Acl::newUser($userModel);

        Acl::macro('test', function ($userModel) {
            return $this->user($userModel);
        });

        $this->assertTrue(
            Acl::hasMacro('test'),
            'Failed to assert that the new macro has been correctly registered.'
        );

        $this->assertSame(
            Acl::user($userModel),
            Acl::test($userModel),
            'Failed to assert that the macro function is working correctly.'
        );

        Acl::clearMacros();

        $this->assertFalse(
            Acl::hasMacro('test'),
            'Failed to assert that all macros have been correctly cleared.'
        );

        $this->expectException(BadMethodCallException::class);

        Acl::test($userModel);
    }
}
