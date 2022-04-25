<?php
declare(strict_types=1);

namespace Stk2k\PowerPDO\context;

use PDO;
use PDOStatement;

use Stk2k\PowerPDO\sql\Join;
use Stk2k\PowerPDO\util\ArrayUtil;

class CountContext extends BaseContext
{
    private $field;
    private $table;
    private $table_alias;
    private $where;     /* array */
    private $placeholders;
    private $distinct;

    /** @var Join[] */
    private $joins;

    /**
     * DISTINCT
     */
    public function distinct() : self
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * SELECT fields
     */
    public function field(string $field = '*') : self
    {
        $this->field = $field;
        return $this;
    }

    /**
     * specifies table name
     */
    public function from(string $table, string $table_alias = "") : self
    {
        $this->table = $table;
        $this->table_alias = $table_alias;
        return $this;
    }

    /**
     * WHERE caluse
     */
    public function where(string $where_clause, array $placeholders = []) : self
    {
        $this->where[] = $where_clause;
        if (!empty($placeholders)){
            $this->placeholders = ArrayUtil::merge($this->placeholders ?? [], $placeholders);
        }
        return $this;
    }

    /**
     * INNERT JOIN
     */
    public function innerJoin(string $table, string $on) : self
    {
        $this->joins[] = new Join(Join::JOIN_TYPE_INNER, $table, $on);
        return $this;
    }

    /**
     * bind values
     */
    public function bind(array $values) : self
    {
        $this->placeholders = ArrayUtil::merge($this->placeholders, $values);
        return $this;
    }

    /**
     * count total number of records
     *
     * @return int                   if failed, returns -1
     */
    public function get() : int
    {
        $stmt = $this->executeSQL();

        if ($row = $stmt->fetch()){
            $val = $row[0] ?? -1;
            return ctype_digit($val) ? intval($val) : -1;
        }
        return -1;
    }

    /**
     * execute SQL
     *
     * @return PDOStatement
     */
    private function executeSQL() : PDOStatement
    {
        $pdo = $this->getPDO();
        $logger = $this->getLogger();

        // generate SQL
        $sql = $this->buildSelectSQL();

        $logger->debug("SQL: {$sql}");

        // prepare SQL
        $stmt = $pdo->prepare($sql);

        // specifies placeholders
        if (is_array($this->placeholders)){
            foreach($this->placeholders as $k => $v)
            {
                $stmt->bindValue($k, $v);
                $logger->debug("binded: [{$k}]={$v}");
            }
        }

        // fetch mode: CLASS
        $stmt->setFetchMode(PDO::FETCH_NUM);

        // execute SQL
        $stmt->execute();

        return $stmt;
    }

    /**
     * build SELECT sql
     *
     */
    private function buildSelectSQL() : string
    {
        // SELECT
        $sql[] = $this->distinct ? "SELECT DISTINCT COUNT({$this->field})" : "SELECT COUNT({$this->field})";

        // FROM
        if (empty($this->table_alias))
        {
            $sql[] = "FROM {$this->table}";
        }
        else {
            $sql[] = "FROM {$this->table} AS {$this->table_alias}";
        }

        // JOIN
        if (is_array(($this->joins)))
        {
            foreach($this->joins as $join)
            {
                $sql[] = $join->toSQL();
            }
        }

        // WHERE
        $sql[] = "WHERE";
        $where = [];
        foreach($this->where as $item){
            $where[] = $item;
        }
        $sql[] = implode(" AND ", $where);

        return  implode(" ", $sql);
    }
}