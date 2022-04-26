<?php /** @noinspection ALL */
declare(strict_types=1);
namespace Stk2k\PowerPDO;

use PDO;
use PDOStatement;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use Stk2k\PowerPDO\context\CountContext;
use Stk2k\PowerPDO\context\InsertContext;
use Stk2k\PowerPDO\context\SelectContext;
use Stk2k\PowerPDO\context\UpdateContext;
use Stk2k\PowerPDO\sql\SQL;

class PowerPDO
{
    private $pdo;
    private $logger;
    private $last_sql;

    /**
     * constructor
     *
     * @param PDO $pdo
     * @param ?LoggerInterface $logger
     * @param ?array $pdo_options
     */
    public function __construct(
        PDO $pdo,
        LoggerInterface $logger = null,
        array $pdo_options = null)
    {
        $this->pdo = $pdo;
        $this->logger = $logger ?? new NullLogger;

        if (is_array($pdo_options)){
            foreach($pdo_options as $k => $v){
                $pdo->setAttribute($k, $v);
            }
        }
    }

    /**
     * PDO
     */
    public function getPDO() : PDO
    {
        return $this->pdo;
    }

    /**
     * Logger
     */
    public function getLogger() : LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Returns last executed SQL
     *
     * @return SQL
     */
    public function getLastSQL() : SQL
    {
        return $this->last_sql;
    }

    /**
     * Set last executed SQL
     */
    protected function setLastSQL(SQL $sql)
    {
        $this->last_sql = $sql;
    }

    /**
     * SELECT
     */
    public function select(string $fields = null) : SelectContext
    {
        if ($fields == null){
            return  new SelectContext($this, $this->logger);
        }
        return (new SelectContext($this, $this->logger))->fields($fields);
    }

    /**
     * COUNT
     */
    public function count(string $field = null) : CountContext
    {
        if ($field == null){
            return  (new CountContext($this, $this->logger))->field();
        }
        return (new CountContext($this, $this->logger))->field($field);
    }

    /**
     * INSERT
     */
    public function insert() : InsertContext
    {
        return  new InsertContext($this, $this->logger);
    }

    /**
     * UPDATE
     */
    public function update(string $table = null) : UpdateContext
    {
        if ($table == null){
            return  new UpdateContext($this, $this->logger);
        }
        return (new UpdateContext($this, $this->logger))->table($table);
    }

    /**
     * DELETE
     */
    public function delete() : UpdateContext
    {
        return  new UpdateContext($this, $this->logger);
    }

    /**
     * Execute SQL
     *
     * @param string $sql
     * @param array|null $params
     *
     * @return PDOStatement
     */
    public function execute(string $sql, array $params = null) : PDOStatement
    {
        // prepare SQL
        $stmt = $this->pdo->prepare($sql);

        // specifies placeholders
        if (is_array($params)){
            foreach($params as $k => $v)
            {
                $stmt->bindValue($k, $v);
                $this->logger->debug("binded: [{$k}]={$v}");
            }
        }

        // update last SQL
        $this->last_sql = new SQL($sql, $params);

        $this->logger->debug("SQL: {$sql}");

        // execute SQL
        $stmt->execute();

        return $stmt;
    }

    /**
     * Fetch record(s)
     *
     * @param string $sql
     * @param array|null $params
     *
     * @return int
     */
    public function fetchNumber(string $sql, array $params = null) : int
    {
        $stmt = $this->prepareSQL($sql, $params);

        $row = $stmt->fetch(PDO::FETCH_NUM);

        if (is_array($row)){
            $val = $row[0] ?? -1;
            return ctype_digit($val) ? intval($val) : -1;
        }

        return -1;
    }

    /**
     * Fetch record(s) for class
     *
     * @param string $class
     * @param string $sql
     * @param array|null $params
     *
     * @return array
     */
    public function fetchAll(string $class, string $sql, array $params = null) : array
    {
        $stmt = $this->prepareSQL($sql, $params);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, $class);
    }

    /**
     * Fetch a record for class
     *
     * @param string $class
     * @param string $sql
     * @param array|null $params
     *
     * @return mixed
     */
    public function fetchObject(string $class, string $sql, array $params = null)
    {
        $stmt = $this->prepareSQL($sql, $params);

        $stmt->execute();

        return $stmt->fetchObject($class);
    }

    /**
     * Prepare executing SQL
     *
     * @param string $sql
     * @param array|null $params
     *
     * @return mixed
     */
    private function prepareSQL(string $sql, array $params = null)
    {
        // prepare SQL
        $stmt = $this->pdo->prepare($sql);

        // specifies placeholders
        if (is_array($params)){
            foreach($params as $k => $v)
            {
                $stmt->bindValue($k, $v);
                $this->logger->debug("binded: [{$k}]={$v}");
            }
        }

        // update last SQL
        $this->last_sql = new SQL($sql, $params);

        $this->logger->debug("SQL: {$sql}");

        return $stmt;
    }

}