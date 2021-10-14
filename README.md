# Laravel FIQL Parser

[![Latest Stable Version](http://poser.pugx.org/prettus/laravel-fiql-parser/v)](https://packagist.org/packages/prettus/laravel-fiql-parser)
[![Total Downloads](http://poser.pugx.org/prettus/laravel-fiql-parser/downloads)](https://packagist.org/packages/prettus/laravel-fiql-parser)
[![Latest Unstable Version](http://poser.pugx.org/prettus/laravel-fiql-parser/v/unstable)](https://packagist.org/packages/prettus/laravel-fiql-parser)
[![License](http://poser.pugx.org/prettus/laravel-fiql-parser/license)](https://packagist.org/packages/prettus/laravel-fiql-parser)
[![Maintainability](https://api.codeclimate.com/v1/badges/cbff811f623998298475/maintainability)](https://codeclimate.com/github/andersao/laravel-fiql-parser/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/cbff811f623998298475/test_coverage)](https://codeclimate.com/github/andersao/laravel-fiql-parser/test_coverage)

## Installation

```bash
composer require prettus/laravel-fiql-parser:dev-main
```

## Features

### Using Query Builder

```php
use Illuminate\Support\Facades\DB;

$query = DB::table('users')->filter('last_name==foo');
print_r($query->toSql());
// select * from `users` where `last_name` = ?
```

### Using Eloquent model

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
}

```

```php
$query = User::query()->filter('last_name==foo');
print_r($query->toSql());
// select * from `users` where `last_name` = ?
```
