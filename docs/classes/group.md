# Rexpl\LaravelAcl\Group

## Table of content

- [Group::groupPermissions()](#groupgrouppermissions)

<br/>

## Group::groupPermissions()

Returns all the permissions directly assigned to this group.

``` php
public function groupPermissions(int $id): Illuminate\Support\Collection
```

### Return values

This method return a `Illuminate\Support\Collection` instance.

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

- **Rexpl\LaravelAcl\Models\Permission|string|int:** The permission name or id. You can also supply the permission model directly.

### Return values

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

- **Rexpl\LaravelAcl\Models\Permission|string|int:** The permission name or id. You can also supply the permission model directly.

### Return values

This method doesn't return a value (void).

### Example

``` php
use Rexpl\LaravelAcl\Group;

Group::find($groupID)->removePermission('user:read');
```
