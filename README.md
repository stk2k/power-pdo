Powerful and thin wrapper of PDO
=======================

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stk2k/power-pdo.svg?style=flat-square)](https://packagist.org/packages/stk2k/power-pdo)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/stk2k/power-pdo.svg?branch=master)](https://travis-ci.org/stk2k/power-pdo)
[![Coverage Status](https://coveralls.io/repos/github/stk2k/power-pdo/badge.svg?branch=master)](https://coveralls.io/github/stk2k/power-pdo?branch=master)
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
use Stk2k\PowerPdo\PowerPDO;

$dsn = 'mysql:dbname=mydatabase;host=localhost';
$user = 'myuser';
$password = 'mypass';
$pdo = new PDO($dsn, $user, $password);

$users = (new PowerPDO($pdo))
    ->select("ID, user_name, nickname, email")
    ->from("users")
    ->where("deleted = 0")
    ->getAll(UserEntity::class);
```

### For SQLite

```php
use Stk2k\PowerPdo\PowerPDO;

$dsn = 'sqlite:/path/to/dbfile_of_sqlite';
$pdo = new PDO($dsn);

$users = (new PowerPDO($pdo))
    ->select("ID, user_name, nickname, email")
    ->from("users")
    ->where("deleted = 0")
    ->getAll(UserEntity::class);
```

### Logging(PSR-3 Logger)

```php
use Stk2k\PowerPdo\PowerPDO;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$dsn = 'sqlite:/path/to/dbfile_of_sqlite';
$pdo = new PDO($dsn);

// monolog
$log = new Logger('name');
$log->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));

$users = (new PowerPDO($pdo, $log))
    ->select("ID, user_name, nickname, email")
    ->from("users")
    ->where("deleted = 0")
    ->getAll(UserEntity::class);
```

### Specifying PDO options

```php
use Stk2k\PowerPdo\PowerPDO;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$dsn = 'sqlite:/path/to/dbfile_of_sqlite';
$pdo = new PDO($dsn, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    PDO::ATTR_TIMEOUT => 10,
]);
```

### Transaction

```php
use Stk2k\PowerPdo\Transaction;
use Stk2k\PowerPdo\exception\TransactionException;

$dsn = 'sqlite:/path/to/dbfile_of_sqlite';
$pdo = new PDO($dsn);

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
use Stk2k\PowerPdo\PowerPDO;

$dsn = 'sqlite:/path/to/dbfile_of_sqlite';
$pdo = new PDO($dsn);

// literal style
(new PowerPDO($pdo))
    ->insert()
    ->into("users", "ID, user_name, nickname, email")
    ->values("123, 'hanako', 'hana', 'hanako@sample.com'")
    ->execute();

// placeholder style
(new PowerPDO($pdo))
    ->insert()
    ->into("users", "ID, user_name, nickname, email")
    ->values(":ID, :user_name, :nickname, :email",[
            ':ID' => 123,
            ':user_name' => 'hanako',
            ':nickname' => 'hana',
            ':email' => 'hanako@sample.com',
        ])
    ->execute();

// entity style
$new_user = new UserEntity();

$new_user->ID = 123;
$new_user->user_name = 'hanako';
$new_user->nickname = 'hana';
$new_user->email = 'hanako@sample.com';

(new PowerPDO($pdo))
    ->insert()
    ->into("users")
    ->values($new_user)
    ->execute();
```

### UPDATE

```php
use Stk2k\PowerPdo\PowerPDO;

$dsn = 'sqlite:/path/to/dbfile_of_sqlite';
$pdo = new PDO($dsn);

// literal style
(new PowerPDO($pdo))
    ->update("users")
    ->set("user_name", "hanako2")
    ->set("email", "hanako2@sample.com")
    ->execute();

// placeholder style
(new PowerPDO($pdo))
    ->update("users")
    ->set("user_name", ":user_name")
    ->set("email", ":email")
    ->bind([
            ':user_name' => 'hanako2',
            ':email' => 'hanako2@sample.com',
        ])
    ->execute();

// entity style
$new_user = new UserEntity();

$new_user->ID = 124;
$new_user->user_name = 'hanako2';
$new_user->nickname = 'hana2';
$new_user->email = 'hanako2@sample.com';

(new PowerPDO($pdo))
    ->update("users")
    ->values($new_user)
    ->execute();
```

### DELETE

```php
use Stk2k\PowerPdo\PowerPDO;

$dsn = 'sqlite:/path/to/dbfile_of_sqlite';
$pdo = new PDO($dsn);

// literal style
(new PowerPDO($pdo))
    ->delete()
    ->from("users")
    ->where("email = 'hanako2@sample.com'")
    ->execute();

// placeholder style
(new PowerPDO($pdo))
    ->delete()
    ->from("users")
    ->where("email = :email")
    ->bind([
            ':email' => 'hanako2@sample.com',
        ])
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


