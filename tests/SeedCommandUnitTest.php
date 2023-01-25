<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Rexpl\LaravelAcl\Acl;
use Rexpl\LaravelAcl\Group;

class SeedCommandUnitTest extends TestBase
{
    use RefreshDatabase;

    /**
     * Test the seeder command.
     * 
     * @return void
     */
    public function testSeedCommand(): void
    {
        $path = base_path('database/seeders/TestAcl.php');

        if (File::exists($path)) File::delete($path);

        Acl::newPermission('user:read');
        Acl::newPermission('user:write');
        Acl::newPermission('user:delete');

        Group::new('Test Group 2')
            ->addPermission('user:read')
            ->addPermission('user:write')
            ->addPermission('user:delete')
            ->addChildGroup(
                Group::new('Test Group 1')
                    ->addPermission('user:read')
                    ->addPermission('user:write')
            )
            ->addChildGroup(
                Group::new('Test Group 3')
                    ->addPermission('user:read')
            );

        Artisan::call('acl:seeder TestAcl');

        $this->assertTrue(
            File::exists($path)
        );

        $content = file_get_contents($path);

        $this->assertEquals(
            file_get_contents(__DIR__.'/seed-test.stub'),
            $content,
            'The content of the freshly made seed is unexpected.'
        );
    }
}