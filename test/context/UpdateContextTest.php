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

class UpdateContextTest extends TestCase
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
        $values = [
            'user_name' => 'William Tiger',
            'nickname' => 'Bill',
            'email' => 'bill@tiger.com',
        ];

        $object = (new PowerPDO($this->pdo))
            ->update()
            ->table("users")
            ->values($values);
        $method = new ReflectionMethod($object, 'buildUpdateSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $sql = $result->getText();
        $params = $result->getParams();

        $params_expected = [
            ':user_name' => 'William Tiger',
            ':nickname' => 'Bill',
            ':email' => 'bill@tiger.com',
        ];

        $this->assertEquals("UPDATE users SET user_name=:user_name,nickname=:nickname,email=:email", $sql);
        $this->assertEquals($params_expected, $params);
    }

    public function testUpdate()
    {
        $values = [
            'deleted' => 1,
            'nickname' => 'Billy'
        ];

        (new PowerPDO($this->pdo))
            ->update("users")
            ->values($values)
            ->where("ID = 1")
            ->execute();

        $stmt = $this->pdo->query("SELECT count(*) FROM users");
        $cnt = $stmt->fetch()[0];

        $this->assertEquals(3, $cnt);

        $stmt = $this->pdo->query("SELECT * FROM users WHERE nickname = 'Billy'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $values_expected = [
            'deleted' => '1',
            'user_name' => 'William Tiger',
            'nickname' => 'Billy',
            'email' => 'bill@tiger.com',
            'ID' => 1
        ];

        $this->assertEquals($values_expected, $row);
    }
}