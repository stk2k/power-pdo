<?php /** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */
declare(strict_types=1);

namespace Stk2k\PowerPDO\Test\context;

use PHPUnit\Framework\TestCase;

use PDO;
use ReflectionException;
use ReflectionMethod;

use Stk2k\PowerPDO\PowerPDO;
use Stk2k\PowerPDO\Test\table\UsersTable;

class DeleteContextTest extends TestCase
{
    const DSN = "sqlite::memory:";

    private $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO(self::DSN);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        UsersTable::init($this->pdo);
    }

    /**
     * @throws ReflectionException
     */
    public function testBuildUpdateSQL()
    {
        $object = (new PowerPDO($this->pdo))
            ->delete()
            ->from("users")
            ->where("nickname LIKE :nickname")
            ->bind(['nickname' => 'Bill']);
        $method = new ReflectionMethod($object, 'buildDeleteSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $sql = $result->getText();
        $params = $result->getParams();

        $this->assertEquals("DELETE FROM users WHERE nickname LIKE :nickname", $sql);
        $this->assertEquals([':nickname' => 'Bill'], $params);
    }

    public function testDelete()
    {
        (new PowerPDO($this->pdo))
            ->delete()
            ->from("users")
            ->where("nickname LIKE :nickname", ["nickname" => "%Bi%"])
            ->execute();

        $stmt = $this->pdo->query("SELECT count(*) FROM users");
        $cnt = $stmt->fetch()[0];

        $this->assertEquals(2, $cnt);
    }
}