<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl;

use Rexpl\LaravelAcl\Models\Group as GroupModel;
use RuntimeException;

final class Group extends BaseGroup
{
    /**
     * Saves the already set instances.
     * 
     * @var array<static>
     */
    protected static $groups = [];


    /**
     * @param GroupModel $group
     * 
     * @return void
     */
    public function __construct(GroupModel $group)
    {
        $this->group = $group;
    }


    /**
     * Returns the group ID.
     * 
     * @return int
     */
    public function id(): int
    {
        return $this->group->id;
    }
    
    
    /**
    * Returns the group model.
    * 
    * @throws RuntimeException
    */
    protected function fetchGroupModel(): GroupModel
    {
        throw new RuntimeException(
            'Called Group::fetchGroupModel()'
        );
    }


    /**
     * returns the group instance.
     * 
     * @param int $id
     * 
     * @return static
     */
    public static function find(int $id): static
    {
        if (isset(static::$groups[$id])) return static::$groups[$id];

        static::$groups[$id] = new static(static::getGroupByID($id));
        return static::$groups[$id];
    }


    /**
     * Deletes the group by ID.
     * 
     * @param int $id
     * @param bool $clean
     * 
     * @return void
     */
    public static function delete(int $id, bool $clean = true): void
    {
        if (isset(static::$groups[$id])) unset(static::$groups[$id]);

        $group = static::getGroupByID($id);

        if ($clean) static::cleanGroup($group->id);

        $group->delete();
    }


    /**
     * Creates new group.
     * 
     * @param string|int $name
     * 
     * @return static
     */
    public static function new(string|int $name): static
    {
        $group = new GroupModel();
        $group->name = $name;
        $group->save();

        static::$groups[$group->id] = new static($group);
        return static::$groups[$group->id];
    }
}