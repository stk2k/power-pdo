<?php /** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */
declare(strict_types=1);

namespace Stk2k\PowerPDO\Test\context;

use PHPUnit\Framework\TestCase;

use PDO;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

use Stk2k\PowerPDO\PowerPDO;

class InsertContextTest extends TestCase
{
    const DSN = "sqlite:" . __DIR__ . '/../test_db.db';

    private $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO(self::DSN);
    }

    /**
     * @throws ReflectionException
     */
    public function testBuildInsertSQL()
    {
        $object = (new PowerPDO($this->pdo))
            ->insert()
            ->into("users")
            ->values([
                'deleted' => 0,
                'user_name' => 'William Tiger',
                'nickname' => 'Bill',
                'email' => 'bill@tiger.com',
            ]);
        $method = new ReflectionMethod($object, 'buildInsertSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $this->assertEquals("INSERT INTO users(deleted,user_name,nickname,email) VALUES(:deleted,:user_name,:nickname,:email)", $result);
    }
}