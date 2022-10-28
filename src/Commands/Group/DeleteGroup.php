<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Group;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Group;

class DeleteGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:delete {id? : Group ID} {--c|clean : Don\'t clean the database from all related data}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Deletes group';

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        Group::delete(
            $this->argument('id') ?? $this->ask('Enter group ID'),
            null === $this->option('clean')
        );

        $this->info('Successfuly deleted group.');
    }
}