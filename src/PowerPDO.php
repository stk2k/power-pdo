<?php
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
use Stk2k\PowerPDO\context\DeleteContext;
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
     * @param ?array $options
     */
    public function __construct(
        PDO $pdo,
        LoggerInterface $logger = null,
        array $options = null)
    {
        $this->pdo = $pdo;
        $this->logger = $logger ?? new NullLogger;

        if (is_array($options)){
            $this->options($options);
        }
    }

    /**
     * Make new PowerPDO object
     */
    public static function make(string $dsn, string $user = null, string $password = null) : self
    {
        return new self(new PDO($dsn, $user, $password));
    }

    /**
     * Specifies logger
     */
    public function logger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Specifies options
     */
    public function options(array $options)
    {
        foreach($options as $k => $v){
            $this->pdo->setAttribute($k, $v);
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
     */
    public function getLastSQL()
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
    public function update(string $table) : UpdateContext
    {
        return new UpdateContext($this, $this->logger, $table);
    }

    /**
     * DELETE
     */
    public function delete() : DeleteContext
    {
        return  new DeleteContext($this, $this->logger);
    }

    /**
     * Execute SQL
     *
     * @param SQL $sql
     *
     * @return PDOStatement
     */
    public function execute(SQL $sql) : PDOStatement
    {
        // update last SQL
        $this->last_sql = $sql;

        $this->logger->debug("SQL: {$sql}");

        // prepare SQL
        $stmt = $this->pdo->prepare($sql->getText());

        // specifies placeholders
        if (is_array($sql->getParams())){
            foreach($sql->getParams() as $k => $v)
            {
                $stmt->bindValue($k, $v);
                $this->logger->debug("binded: [{$k}]={$v}");
            }
        }

        // execute SQL
        $stmt->execute();

        return $stmt;
    }

    /**
     * Fetch record(s)
     *
     * @param SQL $sql
     *
     * @return int
     */
    public function fetchNumber(SQL $sql) : int
    {
        $stmt = $this->prepareSQL($sql);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_NUM);

        if (is_array($row)){
            $val = $row[0] ?? -1;
            return ctype_digit($val) ? intval($val) : -1;
        }

        return -1;
    }

    /**
     * Fetch record(s) as objects
     *
     * @param string $class
     * @param SQL $sql
     *
     * @return array
     */
    public function fetchAllObjects(string $class, SQL $sql) : array
    {
        $stmt = $this->prepareSQL($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, $class);
    }

    /**
     * Fetch a record as an object
     *
     * @param string $class
     * @param SQL $sql
     *
     * @return mixed
     */
    public function fetchObject(string $class, SQL $sql)
    {
        $stmt = $this->prepareSQL($sql);

        $stmt->execute();

        return $stmt->fetchObject($class);
    }

    /**
     * Fetch record(s)as an assoc array
     *
     * @param SQL $sql
     *
     * @return array
     */
    public function fetchAllAssoc(SQL $sql) : array
    {
        $stmt = $this->prepareSQL($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a record as an assoc array
     *
     * @param SQL $sql
     *
     * @return mixed
     */
    public function fetchAssoc(SQL $sql)
    {
        $stmt = $this->prepareSQL($sql);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Prepare executing SQL
     *
     * @param SQL $sql
     *
     * @return PDOStatement|false
     */
    private function prepareSQL(SQL $sql)
    {
        // update last SQL
        $this->last_sql = $sql;

        $this->logger->debug("SQL: {$sql}");

        // prepare SQL
        $stmt = $this->pdo->prepare($sql->getText());

        // specifies placeholders
        if (is_array($sql->getParams())){
            foreach($sql->getParams() as $k => $v)
            {
                $stmt->bindValue($k, $v);
                $this->logger->debug("binded: [{$k}]={$v}");
            }
        }

        return $stmt;
    }

}