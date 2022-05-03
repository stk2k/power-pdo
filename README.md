Powerful and thin wrapper of PDO
=======================

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stk2k/power-pdo.svg?style=flat-square)](https://packagist.org/packages/stk2k/power-pdo)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://app.travis-ci.com/stk2k/power-pdo.svg?branch=main)
[![Coverage Status](https://coveralls.io/repos/github/stk2k/power-pdo/badge.svg?branch=main)](https://coveralls.io/github/stk2k/power-pdo?branch=main)
[![Code Climate](https://codeclimate.com/github/stk2k/power-pdo/badges/gpa.svg)](https://codeclimate.com/github/stk2k/power-pdo)
[![Total Downloads](https://img.shields.io/packagist/dt/stk2k/power-pdo.svg?style=flat-square)](https://packagist.org/packages/stk2k/power-pdo)

## Description

Powerful and thin wrapper of PDO


## Feature

- Fluent Query Builder
- Supports POPO(Plain Old Php Object) entity class
- Supports SQL JOIN
- PSR-3 Logger

## How to use

### Entity class

```php
class UserEntity
{
    public $ID;
    public $deleted;
    public $user_name;
    public $nickname;
    public $email;
}
```


### For MySQL

```php
use Stk2k\PowerPDO\PowerPDO;

$dsn = 'mysql:dbname=mydatabase;host=localhost';
$user = 'myuser';
$password = 'mypass';

$ppdo = new PowerPDO(new PDO($dsn, $user, $password));
or 
$ppdo = PowerPDO::make($dsn, $user, $password);
```

### For SQLite

```php
use Stk2k\PowerPDO\PowerPDO;

$dsn = 'sqlite:/path/to/dbfile_of_sqlite';
$ppdo = new PowerPDO(new PDO($dsn));
or 
$ppdo = PowerPDO::make($dsn);
```

### Logging(PSR-3 Logger)

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// monolog
$log = new Logger('name');
$log->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));

$ppdo = new PowerPDO(new PDO($dsn), $log);
or 
$ppdo = PowerPDO::make($dsn)->logger($log);
```

### SELECT

```php

// array style
$users = PowerPDO::make($dsn)
    ->select("ID, user_name, nickname, email")
    ->from("users")
    ->where("deleted = 0")
    ->getAll();
foreach($users as $u){
    $uid = $u['ID'];
    $name = $u['user_name'];
    echo "[$uid]$name";
}

// entity style
$users = PowerPDO::make($dsn)
    ->select("ID, user_name, nickname, email")
    ->from("users")
    ->where("deleted = 0")
    ->getAll(UserEntity::class);

foreach($users as $u){
    $uid = $u->ID;
    $name = $u->user_name;
    echo "[$uid]$name";
}
```

### Placeholders(Prepared statement)

```php

$users = PowerPDO::make($dsn)
    ->select("ID, user_name, nickname, email")
    ->from("users")
    ->where("nickname LIKE :nickname")
    ->bind(['nickname' => '%Bi%'])
    ->getAll();
```

### Count

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$users = PowerPDO::make($dsn)
    ->count()
    ->from("users")
    ->where("deleted = 0")
    ->get();
```

### Transaction

```php
use Stk2k\PowerPDO\Transaction;
use Stk2k\PowerPDO\exception\TransactionException;

try{
    $tr = new Transaction($pdo);
    $tr->begin();
    // execute UPDATE/DELETE/INSERT SQL here
    $tr->commit();
}
catch(TransactionException $ex){
    $tr->rollback();
}
```

### INSERT

```php
// array style
PowerPDO::make($dsn)
    ->insert()
    ->into("users", "ID, user_name, nickname, email")
    ->values(
        "ID" => "123",
        "user_name" => "hanako", 
        "nickname" => "hana", 
        "email" => "hanako@sample.com"
    )
    ->execute();

// entity style
$new_user = new UserEntity();

$new_user->ID = 123;
$new_user->user_name = 'hanako';
$new_user->nickname = 'hana';
$new_user->email = 'hanako@sample.com';

PowerPDO::make($dsn)
    ->insert()
    ->into("users")
    ->values($new_user)
    ->execute();
```

### UPDATE

```php
// literal style
PowerPDO::make($dsn)
    ->update("users")
    ->set("user_name", "hanako2")
    ->set("email", "hanako2@sample.com")
    ->where("ID = :ID", ['ID'=>1])
    ->execute();

// array style
PowerPDO::make($dsn)
    ->update("users")
    ->values([
            'user_name' => 'hanako2',
            'email' => 'hanako2@sample.com',
        ])
    ->where("ID = :ID", ['ID'=>1])
    ->execute();

// entity style
$new_user = new UserEntity();

$new_user->user_name = 'hanako2';
$new_user->nickname = 'hana2';
$new_user->email = 'hanako2@sample.com';

PowerPDO::make($dsn)
    ->update("users")
    ->values($new_user)
    ->where("ID = :ID", ['ID'=>1])
    ->execute();
```

### DELETE

```php
use Stk2k\PowerPDO\PowerPDO;

// literal style
PowerPDO::make($dsn)
    ->delete()
    ->from("users")
    ->where("email = 'hanako2@sample.com'")
    ->execute();

// placeholder style
PowerPDO::make($dsn)
    ->delete()
    ->from("users")
    ->where("email = :email",['email' => 'hanako2@sample.com'])
    ->execute();
```

## Requirement

PHP 7.3 or later

## Installing stk2k/power-pdo

The recommended way to install stk2k/power-pdo is through
[Composer](http://getcomposer.org).

```bash
composer require stk2k/power-pdo
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## License
This library is licensed under the MIT license.

## Author

[stk2k](https://github.com/stk2k)

## Disclaimer

This software is no warranty.

We are not responsible for any results caused by the use of this software.

Please use the responsibility of the your self.


