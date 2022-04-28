<?php /** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */
declare(strict_types=1);

namespace Stk2k\PowerPDO\Test\context;

use PHPUnit\Framework\TestCase;

use Exception;

use PDO;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

use Stk2k\PowerPDO\PowerPDO;
use Stk2k\PowerPDO\Test\entity\UserEntity;
use Stk2k\PowerPDO\Test\table\UsersTable;

class SelectContextTest extends TestCase
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
            ->select("ID, user_name, nickname, email")
            ->from("users")
            ->where("deleted = 0");
        $method = new ReflectionMethod($object, 'buildSelectSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $this->assertEquals("SELECT ID, user_name, nickname, email FROM users WHERE deleted = 0", $result->getText());
        $this->assertEquals([], $result->getParams());

        $object = (new PowerPDO($this->pdo))
            ->select("ID, user_name, nickname, email")
            ->from("users")
            ->where("deleted = 0")
            ->where("user_name = :user_name", ['user_name' => 'hanako']);
        $method = new ReflectionMethod($object, 'buildSelectSQL');
        $method->setAccessible(true);
        $result = $method->invoke($object);

        $this->assertEquals("SELECT ID, user_name, nickname, email FROM users WHERE deleted = 0 AND user_name = :user_name", $result->getText());
        $this->assertEquals([':user_name' => 'hanako'], $result->getParams());
    }

    public function testSelect()
    {
        $pdo = new PowerPDO($this->pdo);

        $user = $pdo
            ->select("ID, deleted, user_name, nickname, email")
            ->from("users")
            ->where("deleted = 0")
            ->where("nickname = :nickname", ['nickname' => 'Bill'])
            ->getFirst();

        $user_expected = [
            'deleted' => '0',
            'user_name' => 'William Tiger',
            'nickname' => 'Bill',
            'email' => 'bill@tiger.com',
            'ID' => '1'
        ];

        $this->assertEquals($user_expected, $user);

        try{
            $user = $pdo
                ->select()
                ->from("users")
                ->where("deleted = 0")
                ->where("nickname = :nickname", ['nickname' => 'Bill'])
                ->getFirst(UserEntity::class);
        }
        catch(Exception $e)
        {
            echo $pdo->getLastSQL();
        }

        $user_expected = [
            'deleted' => '0',
            'user_name' => 'William Tiger',
            'nickname' => 'Bill',
            'email' => 'bill@tiger.com',
            'ID' => '1'
        ];

        $this->assertEquals($user_expected, get_object_vars($user));

        try{
            $user = $pdo
                ->select()
                ->from("users")
                ->where("deleted = 0")
                ->where("nickname = :nickname")
                ->bind([':nickname' => 'Bill'])
                ->getFirst(UserEntity::class);
        }
        catch(Exception $e)
        {
            echo $pdo->getLastSQL();
        }

        $user_expected = [
            'deleted' => '0',
            'user_name' => 'William Tiger',
            'nickname' => 'Bill',
            'email' => 'bill@tiger.com',
            'ID' => '1'
        ];

        $this->assertEquals($user_expected, get_object_vars($user));
    }
}