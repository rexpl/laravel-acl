# Rexpl\LaravelAcl\Group

## Table of content

- [Group::find()](#groupfind)
- [Group::new()](#groupnew)
- [Group::delete()](#groupdelete)
- [Group::destroy()](#groupdestroy)
- [Group::groupPermissions()](#groupgrouppermissions)
- [Group::addPermission()](#groupaddpermission)
- [Group::removePermission()](#groupremovepermission)
- [Group::parentGroups()](#groupparentgroups)
- [Group::addParentGroup()](#groupaddparentgroup)
- [Group::removeParentGroup()](#groupremoveparentgroup)
- [Group::childGroups()](#groupchildgroups)
- [Group::addChildGroup()](#groupaddchildgroup)
- [Group::removeChildGroup()](#groupremovechildgroup)

<br/>

## Group::find()

Find a group by id.

``` php
public static function find(int $id): static
```

### Parameters

- **id:** Return the group instance.

### Return value

Returns the group instance.

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::find($id);
```

<br/>

## Group::new()

Creates a new group.

``` php
public static function new(string $name): static
```

### Parameters

- **name:** The group name.

### Return value

Returns the group instance.

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::new('Suppliers');
```

<br/>

## Group::delete()

Deletes a group by id.

``` php
public static function delete(int $id, bool $clean = true): void
```

### Parameters

- **id:** The id of the group wich has to be deleted.
- **clean:** Should the additional data related to the group also be removed from the database (recommended).

### Return value

This method does not return a value (void).

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::delete($id, true);
```

<br/>

## Group::destroy()

Deletes the group.

``` php
public function destroy(bool $clean = true): void
```

### Parameters

- **clean:** Should the additional data related to the group also be removed from the database (recommended).

### Return value

This method does not return a value (void).

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::find($id)->destroy();
```

<br/>

## Group::groupPermissions()

Returns all the permissions directly assigned to this group.

``` php
public function groupPermissions(int $id): Illuminate\Support\Collection
```

### Return value

`Illuminate\Support\Collection`

### Example

``` php
use Rexpl\LaravelAcl\Group;

$allGroupPermissions = Group::find(2)->groupPermissions();
```

<br/>

## Group::addPermission

Add a permission to the group.

``` php
public function addPermission(Rexpl\LaravelAcl\Models\Permission|string|int $permission): void
```

### Parameters 

- **permission:** The permission name or id. You can also supply the permission model directly.

### Return value

This method doesn't return a value (void).

### Example

``` php
use Rexpl\LaravelAcl\Group;

$group = Group::new('Group Name');
$allGroupPermissions = $group->addPermissions('user:read');

var_dump($allGroupPermissions->toArray());

// [
//  [permission_id] => 'user:read'
// ]
```

<br/>

## Group::removePermission()

Remove a permission from the group.

``` php
public function removePermission(Rexpl\LaravelAcl\Models\Permission|string|int $permission): void
```

### Parameters 

- **permission:** The permission name or id. You can also supply the permission model directly.

### Return value

This method doesn't return a value (void).

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::find($groupID)->removePermission('user:read');
```

<br/>

## Group::parentGroups()

Returns all the parents directly assigned to the group.

``` php
public function parentGroups(): Illuminate\Support\Collection
```

### Return values

`Illuminate\Support\Collection`

### Example

``` php
use Rexpl\LaravelAcl\Group;

$allGroupParents = Group::find(2)->parentGroups();
```

<br/>

## Group::addParentGroup()

Add a parent group to the group to inherit permission from this group and give acces to the records of the group to the parent group.

``` php
public function addParentGroup(Rexpl\LaravelAcl\Group|int $group): static
```

### Parameters

- **group:** The group or group id wich has to be added as parent.

### Return value

This method returns the current group instance.

### Example

``` php
use Rexpl\LaravelAcl\Group;

$group = Group::find($groupID);

$group->addParentGroup(Group::find($newParentGroup));
// or
$group->addParentGroup($newParentGroupID);
```

<br/>

## Group::removeParentGroup()

Remove a parent from the group.

``` php
public function removeParentGroup(Rexpl\LaravelAcl\Group|int $group): static
```

### Parameters 

- **group:** The group or group id wich has to be removed.

### Return value

This method returns the current group instance.

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::find($groupID)->removeParentGroup($parentGroup);
```

<br/>

## Group::childGroups()

Returns all the children directly assigned to the group.

``` php
public function childGroups(): Illuminate\Support\Collection
```

### Return values

`Illuminate\Support\Collection`

### Example

``` php
use Rexpl\LaravelAcl\Group;

$allGroupChildrens = Group::find(2)->childGroups();
```

<br/>

## Group::addChildGroup()

Add a child group to the group.

``` php
public function addChildGroup(Rexpl\LaravelAcl\Group|int $group): static
```

### Parameters

- **group:** The group or group id wich has to be added as child.

### Return value

This method returns the current group instance.

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::find($groupID)->addChildGroup(Group::find($newChildGroup));
```

<br/>

## Group::removeChildGroup()

Remove a parent from the group.

``` php
public function removeChildGroup(Rexpl\LaravelAcl\Group|int $group): static
```

### Parameters 

- **group:** The group or group id wich has to be removed.

### Return value

This method returns the current group instance.

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::find($groupID)->removeChildGroup($parentGroup);
```

<br/>

