<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Commands\Record;

use Illuminate\Console\Command;
use Rexpl\LaravelAcl\Acl;
use Rexpl\LaravelAcl\Models\GroupDependency;

class RecordInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record:acl {acronym : Acronym of the model} {id : Record id}';

 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[rexpl/laravel-acl] Display who has access to a record';

 
    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $record = GroupDependency::where('ressource', $this->argument('acronym'))
            ->where('ressource_id', $this->argument('id'))
            ->get();

        if (null === $record) {

            $this->error('Record not found.');
            return;
        }

        $table = [];

        foreach ($record as $value) {

            $group = $value->group;
            
            $table[] = [
                'name' => $group->name ?? 'user:' . $group->user_id,
                'id' => $value->group_id,
                'read' => in_array($value->permission_level, Acl::READ) ? 'Yes' : 'No',
                'write' => in_array($value->permission_level, Acl::WRITE) ? 'Yes' : 'No',
                'delete' => in_array($value->permission_level, Acl::DELETE) ? 'Yes' : 'No',
            ];
        }

        $this->table(
            ['Group name', 'Group ID', 'Read', 'Write', 'Delete'],
            $table
        );
    }
}