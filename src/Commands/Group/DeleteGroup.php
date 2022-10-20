<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Group;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Models\Group;
use Rexpl\LaravelAcl\Models\GroupDependency;
use Rexpl\LaravelAcl\Models\GroupPermission;
use Rexpl\LaravelAcl\Models\GroupUser;
use Rexpl\LaravelAcl\Models\ParentGroup;

class DeleteGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:delete {name? : Name of the group (or ID)} {--c|clean : Clean the database from all related data}';

 
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
        if (!$nameORid = $this->argument('name')) {

            $nameORid = $this->ask('Enter permission name or id');
        }

        $group = is_numeric($nameORid)
            ? Group::find($nameORid)
            : Group::firstWhere('name', $nameORid);

        if (null === $group) {

            $this->error('group ' . $nameORid . ' doesn\'t exists.');
            return;
        }

        $group->delete();

        $name = $group->name ?? 'user:' . $group->user_id;

        $this->info('Successfuly deleted group ' . $name . ' (id: ' . $group->id . ').');

        if (false === $this->option('clean')) return;

        GroupPermission::where('group_id', $group->id)->delete();
        GroupUser::where('group_id', $group->id)->delete();
        ParentGroup::where('child_id', $group->id)
            ->orWhere('parent_id', $group->id)->delete();
        GroupDependency::where('group_id', $group->id)->delete();

        $this->info('Successfuly removed all related data to group.');
    }
}