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

class InsertContextTest extends TestCase
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
    public function testBuildInsertSQL()
    {
        $values = [
            'deleted' => 0,
            'user_name' => 'William Tiger',
            'nickname' => 'Bill',
            'email' => 'bill@tiger.com',
        ];

        $object = (new PowerPDO($this->pdo))
            ->insert()
            ->into("users")
            ->values($values);
        $method = new ReflectionMethod($object, 'buildInsertSQL');
        $method->setAccessible(true);
        $params = null;
        $result = $method->invoke($object);

        $sql = $result->getText();
        $params = $result->getParams();

        $params_expected = [
            ':deleted' => 0,
            ':user_name' => 'William Tiger',
            ':nickname' => 'Bill',
            ':email' => 'bill@tiger.com',
        ];

        $this->assertEquals("INSERT INTO users(deleted,user_name,nickname,email) VALUES(:deleted,:user_name,:nickname,:email)", $sql);
        $this->assertEquals($params_expected, $params);
    }

    public function testInsert()
    {
        $values = [
            'deleted' => 0,
            'user_name' => 'William Tiger',
            'nickname' => 'Bill',
            'email' => 'bill@tiger.com',
        ];

        (new PowerPDO($this->pdo))
            ->insert()
            ->into("users")
            ->values($values)
            ->execute();

        $stmt = $this->pdo->query("SELECT count(*) FROM users");
        $cnt = $stmt->fetch()[0];

        $this->assertEquals(4, $cnt);

        $stmt = $this->pdo->query("SELECT * FROM users WHERE nickname = 'Bill'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $values_expected = [
            'deleted' => '0',
            'user_name' => 'William Tiger',
            'nickname' => 'Bill',
            'email' => 'bill@tiger.com',
            'ID' => 1
        ];

        $this->assertEquals($values_expected, $row);
    }
}