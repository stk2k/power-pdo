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
use Stk2k\PowerPDO\Test\table\UsersTable;

class CountContextTest extends TestCase
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
    public function testBuildSelectSQL()
    {
        $object = (new PowerPDO($this->pdo))
            ->count()
            ->from("users")
            ->where("deleted = 0");
        $method = new ReflectionMethod($object, 'buildSelectSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $this->assertEquals("SELECT COUNT(*) FROM users WHERE deleted = 0", $result->getText());
        $this->assertEquals([], $result->getParams());

        $object = (new PowerPDO($this->pdo))
            ->count('email')
            ->distinct()
            ->from("users")
            ->where("deleted = 0")
            ->where("user_name = :user_name", ['user_name' => 'hanako']);
        $method = new ReflectionMethod($object, 'buildSelectSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $this->assertEquals("SELECT DISTINCT COUNT(email) FROM users WHERE deleted = 0 AND user_name = :user_name", $result->getText());
        $this->assertEquals([':user_name' => 'hanako'], $result->getParams());
    }

    public function testCount()
    {
        $cnt = (new PowerPDO($this->pdo))
            ->count()
            ->from("users")
            ->get();

        $this->assertEquals(3, $cnt);
    }
}