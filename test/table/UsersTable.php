<?php
/** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */
declare(strict_types=1);

namespace Stk2k\PowerPDO\Test\table;

use PDO;

class UsersTable
{
    private static $create_sql = '
CREATE TABLE "users" (
	"ID"	INTEGER,
	"deleted"	INTEGER DEFAULT 0,
	"user_name"	TEXT,
	"nickname"	TEXT,
	"email"	TEXT,
	PRIMARY KEY("ID" AUTOINCREMENT)
)
        ';

    private static $data = [

        [
            'ID' => 1,
            'deleted' => '0',
            'user_name' => 'William Tiger',
            'nickname' => 'Bill',
            'email' => 'bill@tiger.com',
        ],
        [
            'ID' => 2,
            'deleted' => '0',
            'user_name' => 'Adam Smith',
            'nickname' => 'Dam',
            'email' => 'adam@smith.com',
        ],
        [
            'ID' => 3,
            'deleted' => '0',
            'user_name' => 'Amanda Cove',
            'nickname' => 'Ami',
            'email' => 'ami@cove.com',
        ],
    ];

    public static function init(PDO $pdo)
    {
        $pdo->exec(self::$create_sql);

        foreach(self::$data as $item){
            $stmt = $pdo->prepare("INSERT INTO users(ID, deleted, user_name, nickname, email) VALUES(:ID, :deleted, :user_name, :nickname, :email)");
            $stmt->execute($item);
        }
    }
}