<?php /** @noinspection ALL */
declare(strict_types=1);

namespace Stk2k\PowerPDO\context;

use PDO;
use PDOStatement;

use Stk2k\PowerPDO\sql\Join;
use Stk2k\PowerPDO\util\ArrayUtil;
use Stk2k\PowerPDO\sql\SQL;

class CountContext extends BaseContext
{
    private $field;
    private $table;
    private $table_alias;
    private $values;
    private $where;     /* array */
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
    public function where(string $where_clause, array $values = null) : self
    {
        $this->where[] = $where_clause;
        if (is_array($values)){
            $this->values = ArrayUtil::merge($this->values ?? [], $values);
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
        $this->values = ArrayUtil::merge($this->values ?? [], $values);
        return $this;
    }

    /**
     * count total number of records
     *
     * @return int                   if failed, returns -1
     */
    public function get() : int
    {
        // generate SQL
        $sql = $this->buildSelectSQL();

        return $this->getPowerPDO()->fetchNumber($sql);
    }

    /**
     * build SELECT sql
     *
     */
    private function buildSelectSQL() : SQL
    {
        $params = [];

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
        if (is_array($this->where))
        {
            $sql[] = "WHERE";
            $where = [];
            foreach($this->where as $item){
                $where[] = $item;
            }
            $sql[] = implode(" AND ", $where);
        }

        // Placeholders
        if (is_array($this->values)){
            foreach($this->values as $key => $value){
                $params[":{$key}"] = $value;
            }
        }

        return new SQL(implode(" ", $sql), $params);
    }
}