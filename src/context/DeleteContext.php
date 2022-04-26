<?php
/** @noinspection DuplicatedCode */
/** @noinspection SqlNoDataSourceInspection */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Stk2k\PowerPDO\util\ArrayUtil;

class DeleteContext extends BaseContext
{
    private $table;
    private $where;     /* array */
    private $placeholders;

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
    public function where(string $where_clause, array $placeholders = []) : self
    {
        $this->where[] = $where_clause;
        if (!empty($placeholders)){
            $this->placeholders = ArrayUtil::merge($this->placeholders, $placeholders);
        }
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
     * execute SQL
     */
    public function execute() : void
    {
        // generate SQL
        $sql = $this->buildDeleteSQL();

        $this->getPowerPDO()->execute($sql, $this->placeholders);
    }

    /**
     * build DELETE sql
     */
    private function buildDeleteSQL() : string
    {
        // DELETE
        $sql[] = "DELETE FROM {$this->table}";

        // WHERE
        $sql[] = "WHERE";
        foreach($this->where as $where){
            $sql[] = $where;
        }

        return  implode(" ", $sql);
    }
}