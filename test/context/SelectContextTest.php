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

class SelectContextTest extends TestCase
{
    const DSN = "sqlite:";

    private $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO(self::DSN);
    }

    /**
     * @throws ReflectionException
     */
    public function testBuildSelectSQL()
    {
        $object = (new PowerPDO($this->pdo))
            ->select("ID, user_name, nickname, email")
            ->from("users")
            ->where("deleted = 0");
        $method = new ReflectionMethod($object, 'buildSelectSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $this->assertEquals("SELECT ID, user_name, nickname, email FROM users WHERE deleted = 0", $result);

        $object = (new PowerPDO($this->pdo))
            ->select("ID, user_name, nickname, email")
            ->from("users")
            ->where("deleted = 0")
            ->where("user_name = :user_name", [':user_name' => 'hanako']);
        $method = new ReflectionMethod($object, 'buildSelectSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $property = new ReflectionProperty($object, 'placeholders');
        $property->setAccessible(true);
        $value = $property->getValue($object);

        $this->assertEquals("SELECT ID, user_name, nickname, email FROM users WHERE deleted = 0 AND user_name = :user_name", $result);
        $this->assertEquals([':user_name' => 'hanako'], $value);
    }
}