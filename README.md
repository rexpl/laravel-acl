# Laravel ACL

Extensive acl for laravel, including groups and permissions. This package aims to integrate as easily as possible in the laravel ecosystem.

- [Main concept](#main-concept)
- [Installation](#installation)
- [Documentation](/docs/index.md)
- [Changelog](https://github.com/rexpl/laravel-acl/releases)

## Main concept

This packages allows for a great ranges of different options:

- Users can be assigned groups
- Groups can be assigned permissions
- Groups can be assigned parent groups (and the other way arround)
- Groups inherit permissions from the parent groups, parent groups inherit access

#### Exemple:

![Exemple](/docs/img/exemple-readme.jpg)

[Click here to read a detailed explanantion of the exemple above](/docs/exemples/main-exemple.md#main-concept-explained)

## Installation

Require package:

```
composer require rexpl/laravel-acl
```

Run migrations:

```
php artisan migrate
```

Publish configuration (Optional):

```
php artisan vendor:publish --provider="Rexpl\LaravelAcl\ServiceProvider\AclServiceProvider" --tag="config"
```
