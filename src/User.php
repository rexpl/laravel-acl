<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Rexpl\LaravelAcl\Exceptions\{
    UnknownPermissionException,
    ResourceNotFoundException
};
use Rexpl\LaravelAcl\Models\StdAcl;
use Rexpl\LaravelAcl\Models\GroupUser;

class User
{
    /**
     * Saves the already set instances.
     * 
     * @var array
     */
    protected static $users = [];


    /**
     * @param int $id
     * @param array $permissions
     * @param array $groups
     * 
     * @return void
     */
    public function __construct(
        protected int $id = 0,
        protected array $permissions = [],
        protected array $groups = [],
        protected array $stdAcl = [],
        protected ?Group $userGroup = null
    ) {}


    /**
     * See if the user has the permission.
     * 
     * @param string $name
     * 
     * @return bool
     */
    public function canWithPermission(string $name): bool
    {
        return in_array($name, $this->permissions);
    }


    /**
     * Returns all child groups (ids).
     * 
     * @return array
     */
    public function groups(): array
    {
        return $this->groups;
    }


    /**
     * Returns all the permissions the user can use.
     * 
     * @return array
     */
    public function permissions(): array
    {
        return $this->permissions;
    }


    /**
     * Returns the users personal group.
     * 
     * @return Group
     */
    public function userGroup(): Group
    {
        return $this->userGroup ?? $this->initiateUserGroup();
    }


    /**
     * Initiates the users personal group.
     * 
     * @return Group
     */
    protected function initiateUserGroup(): Group
    {
        $this->userGroup = Group::findUserGroup($this->id);

        return $this->userGroup;
    }


    /**
     * Add the group to the user.
     * 
     * @param Group|int $group
     * 
     * @return void
     */
    public function addGroup(Group|int $group): void
    {
        if (is_int($group)) $group = Group::find($group);

        $newUserGroup = new GroupUser();

        $newUserGroup->user_id = $this->id;
        $newUserGroup->group_id = $group->id();

        $newUserGroup->save();
    }


    /**
     * Remove group from the user.
     * 
     * @param Group|int $group
     * 
     * @return void
     */
    public function removeGroup(Group|int $group): void
    {
        if (is_int($group)) $group = Group::find($group);

        GroupUser::where('user_id', $this->id)
            ->where('group_id', $group->id())
            ->delete();
    }


    /**
     * Returns all the groups where the user is in.
     * 
     * @return Collection
     */
    public function allGroups(): Collection
    {
        return GroupUser::where('user_id', $this->id)->get()->load('group');
    }


    /**
     * Add standard acl group.
     * 
     * @param Group|int $group
     * @param int $level
     * 
     * @return void
     */
    public function addStdGroup(Group|int $group, int $level): void
    {
        if (is_int($group)) $group = Group::find($group);

        if (!in_array($level, Acl::RANGE)) {

            throw new UnknownPermissionException(
                'Unknown permission level ' . $level
            );
        }

        StdAcl::updateOrCreate(
            ['user_id' => $this->id, 'group_id' => $group->id()],
            ['permission_level' => $level]
        );
    }


    /**
     * Remove standard acl group.
     * 
     * @param Group|int $group
     * 
     * @return void
     */
    public function removeStdGroup(Group|int $group): void
    {
        if (is_int($group)) $group = Group::find($group);

        StdAcl::where('user_id', $this->id)
            ->where('group_id', $group->id())
            ->delete();
    }


    /**
     * Call when user creates a new record.
     * 
     * @param string $acronym
     * @param int $id
     * 
     * @return Record
     */
    public function create(string $acronym, int $id): Record
    {
        return Record::new($acronym, $id, $this->stdAcl);
    }


    /**
     * Collect all user data.
     * 
     * @param int $idUser
     * 
     * @return array
     */
    protected static function getUserInfo(int $idUser): array
    {
        $allUserGroups = GroupUser::where('user_id', $idUser)->get();

        if (null === $allUserGroups) {

            throw new ResourceNotFoundException(
                'User with id: ' . $idUser . ', not found.'
            );
        }

        $allUserGroups = array_map(
            function ($value)
            {
                return $value['group_id'];
            },
            $allUserGroups->toArray()
        );
        $nFactor = (int) config('acl.nFactor', 3);

        $result = $nFactor !== 0
            ? static::getInheritableData($allUserGroups, $nFactor)
            : static::getNonInheritableData($allUserGroups);

        $stdAcl = StdAcl::select('group_id', 'permission_level')
            ->where('user_id', $idUser)
            ->get()->toArray();

        return [
            'groups' => $result['groups'],
            'permissions' => $result['permissions'],
            'std_acl' => $stdAcl,
        ];
    }


    /**
     * Returns all the parent groups of the user.
     * 
     * @param array $groups
     * @param int $nFactor
     * 
     * @return array
     */
    protected static function groupParentGroups(array $groups, int $nFactor): array
    {
        $query = DB::table('acl_parent_groups')
            ->select(DB::raw('`child_id`, `parent_id`, 1'))
            ->whereIn('child_id', $groups)
            ->unionAll(
                DB::table('acl_parent_groups as e')
                    ->select(DB::raw('`e`.`child_id`, `e`.`parent_id`, `ep`.`n` + 1'))
                    ->join('cte as ep', 'ep.parent_id', '=', 'e.child_id')
                    ->where('ep.n', '<', $nFactor)
            );

        $allParentGroups = DB::table('cte')
            ->select('parent_id')
            ->withRecursiveExpression('cte', $query, ['child_id', 'parent_id', 'n'])
            ->get();

        return array_map(
            function ($value)
            {
                return $value->parent_id;
            },
            $allParentGroups->unique()->toArray()
        );
    }


    /**
     * Return all the child groups of the user.
     * 
     * @param array $groups
     * @param int $nFactor
     * 
     * @return array
     */
    protected static function groupChildGroups(array $groups, int $nFactor): array
    {
        $query = DB::table('acl_parent_groups')
            ->select(DB::raw('`child_id`, `parent_id`, 1'))
            ->whereIn('parent_id', $groups)
            ->unionAll(
                DB::table('acl_parent_groups as e')
                    ->select(DB::raw('`e`.`child_id`, `e`.`parent_id`, `ep`.`n` + 1'))
                    ->join('cte as ep', 'ep.child_id', '=', 'e.parent_id')
                    ->where('ep.n', '<', $nFactor)
            );

        $allChildGroups = DB::table('cte')
            ->select('child_id')
            ->withRecursiveExpression('cte', $query, ['child_id', 'parent_id', 'n'])
            ->get();

        return array_map(
            function ($value)
            {
                return $value->child_id;
            },
            $allChildGroups->unique()->toArray()
        );
    }


    /**
     * Fetches all the permission of the groups.
     * 
     * @param array $group
     * 
     * @return array
     */
    protected static function fetchAllGroupsPermissions(array $groups): array
    {
        $allPermissions = DB::table('acl_group_permissions as g')
            ->select('p.name')
            ->join('acl_permissions as p', 'g.permission_id', '=', 'p.id')
            ->whereIn('g.group_id', $groups)
            ->get();

        return array_map(
            function ($value)
            {
                return $value->name;
            },
            $allPermissions->unique()->toArray()
        );
    }


    /**
     * Retroieve all user inheritance data.
     * 
     * @param array $allUserGroups
     * @param int $nFactor
     * 
     * @return array
     */
    protected static function getInheritableData(array $allUserGroups, int $nFactor): array
    {
        $allChilds = array_unique(array_merge(
            $allUserGroups,    
            static::groupChildGroups($allUserGroups, $nFactor)
        ));

        $allParents = array_unique(array_merge(
            $allUserGroups,    
            static::groupParentGroups($allUserGroups, $nFactor)
        ));
        $allPermissions = static::fetchAllGroupsPermissions($allParents);

        return [
            'groups' => $allChilds,
            'permissions' => $allPermissions,
        ];
    }


    /**
     * Retroieve all user data.
     * 
     * @param array $allUserGroups
     * 
     * @return array
     */
    protected static function getnonInheritableData(array $allUserGroups): array
    {
        $allPermissions = static::fetchAllGroupsPermissions($allUserGroups);

        return [
            'groups' => $allUserGroups,
            'permissions' => $allPermissions,
        ];
    }


    /**
     * Returns the user instance from database or from the cache.
     * 
     * @param int $idUser
     * 
     * @return User
     */
    public static function find(int $idUser): static
    {
        if (isset(static::$users[$idUser])) return static::$users[$idUser];

        if (config('acl.cache', true)) {

            $values = Cache::remember(
                'rexpl_acl_user_' . $idUser,
                config('acl.duration', 604800),
                function () use ($idUser): array
                {
                    return static::getUserInfo($idUser);
                }
            );
        }
        else {

            $values = static::getUserInfo($idUser);
        }        

        static::$users[$idUser] = new static($idUser, $values['permissions'], $values['groups'], $values['std_acl']);

        return static::$users[$idUser];
    }


    /**
     * Creates a new user.
     * 
     * @param int $id
     * 
     * @return static
     */
    public static function new(int $id): static
    {
        $group = Group::new($id, true);

        $user = new GroupUser();
        $user->group_id = $group->id();
        $user->user_id = $id;
        $user->save();

        return new static($id, [], [], [], $group);
    }


    /**
     * Deletes a user.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    public static function delete(int $id, bool $clean): void
    {
        Group::delete($id, $clean, true);
    }
}