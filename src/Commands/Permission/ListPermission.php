<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Permission;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Acl;

class ListPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:list';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] List all permissions';


    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $this->table(
            ['ID', 'Name'],
            Acl::permissions()->toArray()
        );
    }
}