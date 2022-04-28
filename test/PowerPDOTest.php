<?php /** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */
declare(strict_types=1);

namespace Stk2k\PowerPDO\Test;

use PDO;

use PHPUnit\Framework\TestCase;

use Stk2k\PowerPDO\PowerPDO;
use Stk2k\PowerPDO\sql\SQL;
use Stk2k\PowerPDO\Test\entity\UserEntity;
use Stk2k\PowerPDO\Test\table\UsersTable;

class PowerPDOTest extends TestCase
{
    const DSN = "sqlite::memory:";

    private $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO(self::DSN);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        UsersTable::init($this->pdo);
    }

    public function testFetch()
    {
        $pdo = new PowerPDO($this->pdo);

        $user = $pdo->fetchObject(UserEntity::class, new SQL("SELECT * FROM users WHERE nickname = 'Dam'"));

        $this->assertInstanceOf(UserEntity::class, $user);

        $this->assertEquals(UserEntity::Adam(), $user);
    }

    public function testFetchAllObjects()
    {
        $pdo = new PowerPDO($this->pdo);

        $users = $pdo->fetchAllObjects(UserEntity::class, new SQL("SELECT * FROM users"));

        $this->assertIsArray($users);
        $this->assertCount(3, $users);

        $this->assertEquals(UserEntity::Bill(), $users[0]);
        $this->assertEquals(UserEntity::Adam(), $users[1]);
        $this->assertEquals(UserEntity::Amanda(), $users[2]);
    }

    public function testCount()
    {
        $pdo = new PowerPDO($this->pdo);

        $users = $pdo->count()->from("users")->get();

        $this->assertEquals(3, $users);
    }
}