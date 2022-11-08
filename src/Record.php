<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Database\Eloquent\Collection;
use Rexpl\LaravelAcl\Models\GroupDependency;
use Illuminate\Support\Facades\Cache;
use Rexpl\LaravelAcl\Exceptions\UnknownPermissionException;

final class Record
{
    /**
     * Should use cache.
     * 
     * @var bool
     */
    protected $cache;


    /**
     * How long to keep cached values.
     * 
     * @var int
     */
    protected $time;


    /**
     * All the records models
     * 
     * @var Collection
     */
    protected $record;


    /**
     * Are the records fetched.
     * 
     * @var bool
     */
    protected $isRecordFetched = false;


    /**
     * @param string $acronym
     * @param int $id
     * 
     * @return void
     */
    public function __construct(
        protected string $acronym,
        protected int $id
    ) {
        $this->cache = (bool) config('acl.record_cache', true);
        $this->time = (int) config('acl.record_duration', 300);
    }


    /**
     * See if record can be read only based on groups (without validation permissions).
     * 
     * @param User $user
     * 
     * @return bool
     */
    public function canReadRecord(User $user): bool
    {
        return $this->canDoWithRecord($user, 'r', Acl::READ);
    }


    /**
     * See if record can be writen only based on groups (without validation permissions).
     * 
     * @param User $user
     * 
     * @return bool
     */
    public function canWriteRecord(User $user): bool
    {
        return $this->canDoWithRecord($user, 'w', Acl::WRITE);
    }


    /**
     * See if record can be deleted only based on groups (without validation permissions).
     * 
     * @param User $user
     * 
     * @return bool
     */
    public function canDeleteRecord(User $user): bool
    {
        return $this->canDoWithRecord($user, 'd', Acl::DELETE);
    }


    /**
     * Can do with record.
     * 
     * @param User $user
     * @param string $cacheIndex
     * @param array<int> $neededLevel
     * 
     * @return bool
     */
    protected function canDoWithRecord(User $user, string $cacheIndex, array $neededLevel): bool
    {
        if ($this->cache) {

            $result = Cache::remember(
                'rexpl_acl_user_'.$user->id().'_'.$cacheIndex.'_'.$this->acronym.'_'.$this->id,
                $this->time,
                function () use ($user, $neededLevel): bool
                {
                    return $this->loopGroups($user->groups(), $neededLevel);
                }
            );
        } else {

            $result = $this->loopGroups($user->groups(), $neededLevel);
        }
            
        return $result;
    }


    /**
     * Loops through each user group to see if it matches.
     * 
     * @param array<int> $groups
     * @param array<int> $neededLevel
     * 
     * @return bool
     */
    protected function loopGroups(array $groups, array $neededLevel): bool
    {
        foreach ($groups as $group) {
            
            if ($this->isMatch($group, $neededLevel)) return true;
        }

        return false;
    }


    /**
     * See if there is match between the group array and the record array.
     * 
     * @param int $group
     * @param array<int> $neededLevel
     * 
     * @return bool
     */
    protected function isMatch(int $group, array $neededLevel): bool
    {
        foreach ($this->record() as $row) {
            
            if (
                $row->group_id === $group
                && in_array($row->permission_level, $neededLevel)
            ) return true;
        }

        return false;
    }


    /**
     * Return all the depencies.
     * 
     * @return Collection
     */
    public function record(): Collection
    {
        if (!$this->isRecordFetched) {

            $this->record = GroupDependency::where('ressource', $this->acronym)
                ->where('ressource_id', $this->id)
                ->get();
            
            $this->isRecordFetched = true;
        }

        return $this->record;
    }


    /**
     * Assign new group to the record.
     * 
     * @param Group|int $group
     * @param int $level
     * 
     * @return void
     */
    public function assign(Group|int $group, int $level): void
    {
        if (is_int($group)) $group = Group::find($group);

        if (!in_array($level, Acl::RANGE)) {

            throw new UnknownPermissionException(
                'Unknown permission level ' . $level
            );
        }

        GroupDependency::updateOrCreate(
            [
                'ressource' => $this->acronym,
                'ressource_id' => $this->id,
                'group_id' => $group->id()
            ],
            ['permission_level' => $level]
        );
    }


    /**
     * Remove group from record.
     * 
     * @param Group|int $group
     * 
     * @return void
     */
    public function remove(Group|int $group): void
    {
        if (is_int($group)) $group = Group::find($group);

        GroupDependency::where('ressource', $this->acronym)
            ->where('ressource_id', $this->id)
            ->where('group_id', $group->id())
            ->delete();
    }


    /**
     * Refresh the record information.
     * 
     * @return void
     */
    public function refresh()
    {
        $this->isRecordFetched = false;
    }


    /**
     * Make a new record.
     * 
     * @param string $acronym
     * @param int $id
     * @param array<array{group_id:int,permission_level:int}> $groups
     * 
     * @return static
     */
    public static function new(string $acronym, int $id, array $groups): static
    {
        foreach ($groups as $row) {
            
            GroupDependency::create([
                'ressource' => $acronym,
                'ressource_id' => $id,
                'group_id' => $row['group_id'],
                'permission_level' => $row['permission_level'],
            ]);
        }

        return new static($acronym, $id);
    }


    /**
     * Deletes a record.
     * 
     * @param string $acronym
     * @param int $id
     * 
     * @return void
     */
    public static function delete(string $acronym, int $id): void
    {
        GroupDependency::where('ressource', $acronym)
            ->where('ressource_id', $id)
            ->delete();
    }
}