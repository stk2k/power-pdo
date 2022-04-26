<?php /** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */
declare(strict_types=1);

namespace Stk2k\PowerPDO\Test;

use PDO;

use PHPUnit\Framework\TestCase;

use Stk2k\PowerPDO\PowerPDO;
use Stk2k\PowerPDO\Test\entity\UserEntity;

class PowerPDOTest extends TestCase
{
    const DSN = "sqlite:" . __DIR__ . "/test_db.db";

    private $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO(self::DSN);
    }

    public function testFetch()
    {
        $pdo = new PowerPDO($this->pdo);

        $user = $pdo->fetchObject(UserEntity::class, "SELECT * FROM users");

        $this->assertInstanceOf(UserEntity::class, $user);

        $this->assertEquals(UserEntity::Adam(), $user);
    }

    public function testFetchAll()
    {
        $pdo = new PowerPDO($this->pdo);

        $users = $pdo->fetchAll(UserEntity::class, "SELECT * FROM users");

        $this->assertIsArray($users);
        $this->assertCount(2, $users);

        $this->assertEquals(UserEntity::Adam(), $users[0]);
        $this->assertEquals(UserEntity::Amanda(), $users[1]);
    }

}