<?php
/** @noinspection DuplicatedCode */
/** @noinspection SqlNoDataSourceInspection */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use PDO;
use PDOStatement;

use Stk2k\PowerPDO\sql\Join;
use Stk2k\PowerPDO\util\ArrayUtil;

class SelectContext extends BaseContext
{
    private $fields;
    private $table;
    private $table_alias;
    private $where;     /* array */
    private $placeholders;

    /** @var Join[] */
    private $joins;

    /**
     * SELECT fields
     */
    public function fields(string $fields) : self
    {
        $this->fields = $fields;
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
     * get all records
     *
     * @param string $entity_class
     *
     * @return array
     */
    public function getAll(string $entity_class) : array
    {
        // generate SQL
        $sql = $this->buildSelectSQL();

        return $this->getPowerPDO()->fetchAll($entity_class, $sql, $this->placeholders);
    }

    /**
     * get first record
     *
     * @param string $entity_class
     *
     * @return object
     */
    public function getFirst(string $entity_class) : ?object
    {
        // generate SQL
        $sql = $this->buildSelectSQL();

        return $this->getPowerPDO()->fetchObject($entity_class, $sql, $this->placeholders);
    }

    /**
     * build SELECT sql
     */
    private function buildSelectSQL() : string
    {
        // SELECT
        $sql[] = "SELECT {$this->fields}";

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