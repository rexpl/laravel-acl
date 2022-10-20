<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Permission;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Models\GroupPermission;
use Rexpl\LaravelAcl\Models\Permission;

class DeletePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:delete {name? : Name of the permission (or ID)} {--c|clean : Clean the database from all related data}';

 
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
        if (!$nameORid = $this->argument('name')) {

            $nameORid = $this->ask('Enter permission name or id');
        }

        $permission = is_numeric($nameORid)
            ? Permission::find($nameORid)
            : Permission::firstWhere('name', $nameORid);

        if (null === $permission) {

            $this->error('Permission ' . $nameORid . ' doesn\'t exists.');
            return;
        }

        $permission->delete();

        $this->info('Successfuly deleted permission ' . $permission->name . ' (id: ' . $permission->id . ').');

        if (false === $this->option('clean')) return;

        GroupPermission::where('permission_id', $permission->id)->delete();

        $this->info('Successfuly removed all related data to permission.');
    }
}