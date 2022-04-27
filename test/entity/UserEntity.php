<?php
declare(strict_types=1);

namespace Stk2k\PowerPDO\Test\entity;

class UserEntity
{
    public $ID;
    public $deleted;
    public $user_name;
    public $nickname;
    public $email;

    public static function Bill() : UserEntity
    {
        $user = new UserEntity();

        $user->ID = 1;
        $user->deleted = '0';
        $user->user_name = "William Tiger";
        $user->nickname = "Bill";
        $user->email = "bill@tiger.com";

        return $user;
    }
    public static function Adam() : UserEntity
    {
        $user = new UserEntity();

        $user->ID = 2;
        $user->deleted = '0';
        $user->user_name = "Adam Smith";
        $user->nickname = "Dam";
        $user->email = "adam@smith.com";

        return $user;
    }
    public static function Amanda() : UserEntity
    {
        $user = new UserEntity();

        $user->ID = 3;
        $user->deleted = '0';
        $user->user_name = "Amanda Cove";
        $user->nickname = "Ami";
        $user->email = "ami@cove.com";

        return $user;
    }

}