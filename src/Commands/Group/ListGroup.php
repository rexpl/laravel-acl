<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Group;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Models\Group;

class ListGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:list';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] List all groups';

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $this->table(
            ['ID', 'Name', 'User ID'],
            Group::all()->toArray()
        );
    }
}