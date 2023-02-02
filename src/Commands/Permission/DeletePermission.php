<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Permission;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Facades\Acl;

class DeletePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:delete {id? : Permission ID} {--c|clean : Don\'t clean the database from all related data}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Deletes permission';

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        Acl::deletePermission(
            intval($this->argument('id') ?? $this->ask('Enter permission ID')),
            null === $this->option('clean')
        );

        $this->info('Successfuly deleted permission.');
    }
}