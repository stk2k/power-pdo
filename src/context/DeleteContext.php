<?php
/** @noinspection DuplicatedCode */
/** @noinspection SqlNoDataSourceInspection */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Stk2k\PowerPDO\util\ArrayUtil;
use Stk2k\PowerPDO\sql\SQL;

class DeleteContext extends BaseContext
{
    private $table;
    private $values;
    private $where;     /* array */

    /**
     * specifies table name
     */
    public function from(string $table) : self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * WHERE caluse
     */
    public function where(string $where_clause, array $values = null) : self
    {
        $this->where[] = $where_clause;
        if (is_array($values)){
            $this->values = ArrayUtil::merge($this->values, $values);
        }
        return $this;
    }

    /**
     * bind values
     */
    public function bind(array $values) : self
    {
        $this->values = ArrayUtil::merge($this->values, $values);
        return $this;
    }

    /**
     * execute SQL
     */
    public function execute() : void
    {
        // generate SQL
        $sql = $this->buildDeleteSQL();

        $this->getPowerPDO()->execute($sql);
    }

    /**
     * build DELETE sql
     */
    private function buildDeleteSQL() : SQL
    {
        $params = [];

        // DELETE
        $sql[] = "DELETE FROM {$this->table}";

        // WHERE
        if ($this->where)
        {
            $sql[] = "WHERE";
            foreach($this->where as $where){
                $sql[] = $where;
            }
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