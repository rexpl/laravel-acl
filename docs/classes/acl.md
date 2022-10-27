# Rexpl\LaravelAcl\Acl

## Table of content

- [Acl::user()](#acluser)
- [Acl::newUser()](#aclnewuser)
- [Acl::deleteUser()](#acldeleteuser)
- [Acl::group()](#aclgroup)
- [Acl::newGroup()](#aclnewgroup)
- [Acl::deleteGroup()](#acldeletegroup)
- [Acl::record()](#aclrecord)
- [Acl::newRecord()](#aclnewrecord)
- [Acl::deleteRecord()](#acldeleterecord)
- [Acl::newPermission()](#aclnewpermission)
- [Acl::deletePermission()](#acldeletepermission)
- [Acl::permissionID()](#aclpermissionid)
- [Acl::permissionName](#aclpermissionname)
- [Acl::permissions](#aclpermissions)
- [Acl::buildGates](#aclbuildgates)

<br/>

## Acl::user()

This function fecthes the given acl user.

``` php
public static function user(int $id): User
```

### Parameters 

- **id:** The user ID.

### Return values

This function return a `Rexpl\LaravelAcl\User` instance.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

Acl::user(2)->canWithPermission('user:read');

// or

$user = Acl::user(2);
```

> ### :information_source: Rexpl\LaravelAcl\User::find()
> This method is just a shorthand and provides the exact same function as `Rexpl\LaravelAcl\User::find()`.

<br/>

## Acl::newUser()

This function creates a new acl user.

``` php
public static function newUser(int $id): User
```

### Parameters 

- **id:** The user ID.

### Return values

This function return a `Rexpl\LaravelAcl\User` instance.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

$user = Acl::newUser(2);
```

> ### :information_source: Rexpl\LaravelAcl\User::new()
> This method is just a shorthand and provides the exact same function as `Rexpl\LaravelAcl\User::new()`.

<br/>

## Acl::deleteUser()

This function deletes the given acl user.

``` php
public static function deleteUser(int $id, bool $clean = true): void
```

### Parameters 

- **id:** The user ID.
- **clean:** Whether or not to clean all related data to this user (recommended).

### Return values

This function doesn't return a value (void).

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

Acl::deleteUser(2, true);
```

> ### :information_source: Rexpl\LaravelAcl\User::delete()
> This method is just a shorthand and provides the exact same function as `Rexpl\LaravelAcl\User::delete()`.

<br/>

## Acl::group()

This function fecthes the given acl group.

``` php
public static function group(int $id): Group
```

### Parameters 

- **id:** The user ID.

### Return values

This function return a `Rexpl\LaravelAcl\Group` instance.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

Acl::group(2)->removePermission('user:read');

// or

$group = Acl::group(2);
```

> ### :information_source: Rexpl\LaravelAcl\Group::find()
> This method is just a shorthand and provides the exact same function as `Rexpl\LaravelAcl\Group::find()`.

<br/>

## Acl::newGroup()

This function creates a new acl group.

``` php
public static function newGroup(string $name): Group
```

### Parameters 

- **name:** The group name.

### Return values

This function return a `Rexpl\LaravelAcl\Group` instance.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

$group = Acl::newGroup('Admin');
```

> ### :information_source: Rexpl\LaravelAcl\Group::new()
> This method is just a shorthand for `Rexpl\LaravelAcl\Group::new($name, false)`.

<br/>


## Acl::deleteGroup()

This function deletes the given acl group.

``` php
public static function deleteGroup(int $id, bool $clean = true): void
```

### Parameters 

- **id:** The group ID.
- **clean:** Whether or not to clean all related data to this group (recommended).

### Return values

This function doesn't return a value (void).

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

Acl::deleteGroup(2, true);
```

> ### :information_source: Rexpl\LaravelAcl\Group::delete()
> This method is just a shorthand for `Rexpl\LaravelAcl\Group::new($id, $clean, false)`.

<br/>

## Acl::record()

This function fecthes the given acl record.

``` php
public static function record(string $acronym, int $id): Record
```

### Parameters 

- **acronym:** Acronym for the model.
- **id:** The record ID.

### Return values

This function return a `Rexpl\LaravelAcl\Record` instance.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

Acl::record(2)->assign(3, Acl::RWD);

// or

$record = Acl::record(2);
```

<br/>

## Acl::newRecord()

This function creates a new acl record.

``` php
public static function newRecord(string $acronym, int $id): Record
```

### Parameters 

- **acronym:** Acronym for the model.
- **id:** The record ID.

### Return values

This function return a `Rexpl\LaravelAcl\Record` instance.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

$group = Acl::newRecord('usr', 54);
```

> ### :information_source: Rexpl\LaravelAcl\Record::new()
> This method is just a shorthand for `Rexpl\LaravelAcl\Record::new($acronym $id, [])`.

<br/>

## Acl::deleteRecord()

This function deletes the given acl record.

``` php
public static function deleteRecord(string $acronym, int $id): void
```

### Parameters 

- **acronym:** Acronym for the model.
- **id:** The record ID.

### Return values

This function doesn't return a value (void).

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

Acl::deleteRecord('usr', 54);
```

> ### :information_source: Rexpl\LaravelAcl\Record::delete()
> This method is just a shorthand and provides the exact same function as `Rexpl\LaravelAcl\Record::delete()`.

<br/>

## Acl::newPermission()

This function creates a new permission.

``` php
public static function newPermission(string $name): int
```

### Parameters 

- **name:** The permission name.

### Return values

This function returns the permission ID.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

$id = Acl::newPermission('user:read');
```

<br/>

## Acl::deletePermission()

This function deletes the given permission.

``` php
public static function deletePermission(int $id): void
```

### Parameters 

- **id:** The permission ID.

### Return values

This function doesn't return a value (void).

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

Acl::deletePermission($id);
```

<br/>

## Acl::permissionName()

This function finds the permission name by ID.

``` php
public static function permissionName(int $id): ?string
```

### Parameters 

- **id:** The permission ID.

### Return values

Returns the permission name. Returns null if permission is not found.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

$name = Acl::permissionName($id);
```

<br/>

## Acl::permissionID()

This function finds the permission ID by name.

``` php
public static function permissionID(string $name): ?int
```

### Parameters 

- **name:** The permission name.

### Return values

Returns the permission id. Returns null if permission is not found.

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

$id = Acl::permissionID($name);
```

<br/>

## Acl::permissions()

This function returns all permissions.

``` php
public static function permissions(): Collection
```

### Parameters 

This function doesn't have any parameters.

### Return values

This function return a `Illuminate\Database\Eloquent\Collection` instance (containing all permissions).

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

$permissions = Acl::permissions();
```

<br/>

## Acl::buildGates()

This function defines gates for all permissions.

``` php
public static function buildGates(): void
```

### Parameters 

This function doesn't have any parameters.

### Return values

This function doesn't return a value (void).

### Exemple

``` php
use Rexpl\LaravelAcl\Acl;

Acl::buildGates();
```
