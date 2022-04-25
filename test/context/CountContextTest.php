<?php /** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */
declare(strict_types=1);

namespace context;

use PHPUnit\Framework\TestCase;

use PDO;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

use Stk2k\PowerPDO\PowerPDO;

class CountContextTest extends TestCase
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
            ->count()
            ->from("users")
            ->where("deleted = 0");
        $method = new ReflectionMethod($object, 'buildSelectSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $this->assertEquals("SELECT COUNT(*) FROM users WHERE deleted = 0", $result);

        $object = (new PowerPDO($this->pdo))
            ->count('email')
            ->distinct()
            ->from("users")
            ->where("deleted = 0")
            ->where("user_name = :user_name", [':user_name' => 'hanako']);
        $method = new ReflectionMethod($object, 'buildSelectSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $property = new ReflectionProperty($object, 'placeholders');
        $property->setAccessible(true);
        $value = $property->getValue($object);

        $this->assertEquals("SELECT DISTINCT COUNT(email) FROM users WHERE deleted = 0 AND user_name = :user_name", $result);
        $this->assertEquals([':user_name' => 'hanako'], $value);
    }
}