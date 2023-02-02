<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Database\Eloquent\Collection;
use Rexpl\LaravelAcl\Models\GroupDependency;
use Illuminate\Support\Facades\Cache;
use Rexpl\LaravelAcl\Exceptions\UnknownPermissionException;
use Rexpl\LaravelAcl\Internal\PackageUtility;

final class Record
{
    use PackageUtility;

    /**
     * Permission level for read.
     * 
     * @var int
     */
    public const READ = 4;


    /**
     * Permission level for write.
     * 
     * @var int
     */
    public const WRITE = 2;


    /**
     * Permission level for delete.
     * 
     * @var int
     */
    public const DELETE = 1;


    /**
     * All allowed numbers.
     * 
     * @var array<int>
     */
    public const FULL_RANGE = [1, 2, 3, 4, 5, 6, 7];


    /**
     * Permission levels wich contain read permission.
     * 
     * @var array<int>
     */
    protected const READ_RANGE = [4, 5, 6, 7];


    /**
     * Permission levels wich contain write permission.
     * 
     * @var array<int>
     */
    protected const WRITE_RANGE = [2, 3, 6, 7];


    /**
     * Permission levels wich contain delete permission.
     * 
     * @var array<int>
     */
    protected const DELETE_RANGE = [1, 3, 5, 7];

    
    /**
     * Should use cache.
     * 
     * @var bool
     */
    protected bool $cache;


    /**
     * How long to keep cached values.
     * 
     * @var int
     */
    protected int $time;


    /**
     * All the records models
     * 
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected Collection $record;


    /**
     * Are the records fetched.
     * 
     * @var bool
     */
    protected bool $isRecordFetched = false;


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
     * @param \Rexpl\LaravelAcl\User $user
     * 
     * @return bool
     */
    public function canReadRecord(User $user): bool
    {
        return $this->canDoWithRecord($user, 'r', self::READ_RANGE);
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
        return $this->canDoWithRecord($user, 'w', self::WRITE_RANGE);
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
        return $this->canDoWithRecord($user, 'd', self::DELETE_RANGE);
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
     * @return \Illuminate\Database\Eloquent\Collection
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
     * @param \Rexpl\LaravelAcl\Group|int $group
     * @param int $level
     * 
     * @return void
     */
    public function assign(Group|int $group, int $level): void
    {
        if (!in_array($level, self::FULL_RANGE)) {

            throw new UnknownPermissionException(
                'Unknown permission level ' . $level
            );
        }

        GroupDependency::updateOrCreate(
            [
                'ressource' => $this->acronym,
                'ressource_id' => $this->id,
                'group_id' => $this->validGroup($group)->id()
            ],
            ['permission_level' => $level]
        );
    }


    /**
     * Remove group from record.
     * 
     * @param \Rexpl\LaravelAcl\Group|int $group
     * 
     * @return void
     */
    public function remove(Group|int $group): void
    {
        GroupDependency::where('ressource', $this->acronym)
            ->where('ressource_id', $this->id)
            ->where('group_id', $this->validGroup($group)->id())
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
     * Delete the record.
     * 
     * @return void
     */
    public function delete(): void
    {
        GroupDependency::where('ressource', $this->acronym)
            ->where('ressource_id', $this->id)
            ->delete();
    }
}