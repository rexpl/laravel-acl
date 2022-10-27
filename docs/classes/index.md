# Classes

This guide provides a short description about the most usefull classes for you to interact with.

- [Rexpl\LaravelAcl\Acl](/docs/classes/acl.md)
- [Rexpl\LaravelAcl\Group](/docs/classes/group.md)
- [Rexpl\LaravelAcl\Policy](/docs/classes/policy.md)
- [Rexpl\LaravelAcl\Record](/docs/classes/record.md)
- [Rexpl\LaravelAcl\User](/docs/classes/user.md)

## Rexpl\LaravelAcl\Acl

The `Rexpl\LaravelAcl\Acl` provides a set of static method to execute all the most standard action you would typically need. It is the entry point for most other classes (eg: `Rexpl\LaravelAcl\Acl::user(id)` returns a `Rexpl\LaravelAcl\User` instance). [read more](/docs/classes/acl.md)

## Rexpl\LaravelAcl\Group

The `Rexpl\LaravelAcl\Group` provides all actions you need to administrate a group (permission, parents ..). [read more](/docs/classes/group.md)

## Rexpl\LaravelAcl\Policy

You typically will never directly interact `Rexpl\LaravelAcl\Policy`, this class will allow you to write policies integrated to this package in a matter of seconds. [read more](/docs/classes/policy.md)

## Rexpl\LaravelAcl\Record

The `Rexpl\LaravelAcl\Record` provides all actions you need to administrate a record (groups). [read more](/docs/classes/record.md)

## Rexpl\LaravelAcl\User

The `Rexpl\LaravelAcl\User` provides all actions you need to administrate users (groups, standard acl ..). [read more](/docs/classes/user.md)