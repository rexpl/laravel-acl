<?php

declare(strict_types=1);

namespace Rexpl\LaravelAcl\Internal;

use Illuminate\Database\Eloquent\Collection;
use Rexpl\LaravelAcl\Exceptions\ResourceNotFoundException;
use Rexpl\LaravelAcl\Models\Group as GroupModel;
use Rexpl\LaravelAcl\Models\GroupPermission;
use Rexpl\LaravelAcl\Models\Permission;

trait GroupPermissions
{
    /**
     * Returns the group model.
     * 
     * @return \Rexpl\LaravelAcl\Models\Group
     */
    abstract protected function group(): GroupModel;


    /**
     * Returns all direct permissions attached to the group.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function directPermissions(): Collection
    {
        return $this->group()->permissions;
    }


    /**
     * Return all permissions.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     * 
     * @deprecated 1.0 Use directPermissions() instead.
     */
    public function groupPermissions(): Collection
    {
        return $this->directPermissions();
    }


    /**
     * Adds a permission to the group.
     * 
     * @param \Rexpl\LaravelAcl\Models\Permission|string|int $permission
     * 
     * @return static
     */
    public function addPermission(Permission|string|int $permission): static
    {
        GroupPermission::create([
            'permission_id' => $this->fetchPermission($permission)->id,
            'group_id' => $this->group()->id,
        ]);

        return $this;
    }


    /**
     * Removes a permission from the group.
     * 
     * @param \Rexpl\LaravelAcl\Models\Permission|string|int $permission
     * 
     * @return static
     */
    public function removePermission(Permission|string|int $permission): static
    {
        GroupPermission::where('group_id', $this->group()->id)
            ->where('permission_id', $this->fetchPermission($permission)->id)
            ->delete();

        return $this;
    }


    /**
     * Fetches the permission model to be sure it exist
     * 
     * @param \Rexpl\LaravelAcl\Models\Permission|string|int $permission
     * 
     * @return \Rexpl\LaravelAcl\Models\Permission
     */
    protected function fetchPermission(Permission|string|int $permission): Permission
    {
        if (!$permission instanceof Permission) {

            $result = is_numeric($permission)
                ? Permission::find($permission)
                : Permission::firstWhere('name', $permission);

            if (null === $result) {        
                throw new ResourceNotFoundException(sprintf(
                    'Permission %s, not found.', $permission
                ));
            }

            $permission = $result;
        }        

        return $permission;
    }
}