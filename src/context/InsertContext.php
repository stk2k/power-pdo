<?php
/** @noinspection DuplicatedCode */
/** @noinspection SqlNoDataSourceInspection */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Stk2k\PowerPDO\exception\PowerPdoException;
use Stk2k\PowerPDO\sql\SQL;

class InsertContext extends BaseContext
{
    private $table;
    private $values;

    /**
     * specifies table name
     */
    public function into(string $table) : self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * specifies table values
     *
     * @param string|object|array $values
     *
     * @return $this
     */
    public function values($values) : self
    {
        $this->values = $values;
        return $this;
    }

    /**
     * execute SQL
     * @throws PowerPdoException
     */
    public function execute() : void
    {
        // generate SQL
        $sql = $this->buildInsertSQL();

        // execute SQL
        $this->getPowerPDO()->execute($sql);
    }

    /**
     * build INSERT sql
     *
     * @return SQL
     *
     * @throws
     */
    private function buildInsertSQL() : SQL
    {
        // fields from values key
        $values_map = null;
        $params = [];
        if (is_array($this->values))
        {
            $values_map = $this->values;
        }
        else if (is_object($this->values))
        {
            $values_map = get_object_vars($this->values);
        }
        else
        {
            throw new PowerPdoException("Insert requires values!");
        }
        $field_placeholders = [];
        foreach($values_map as $key => $value)
        {
            $field_placeholders[] = ":{$key}";
            $params[":{$key}"] = $value;
        }

        // INSERT INTO(fields, ...)
        $insert_fields = implode(",", array_keys($values_map));
        $sql[] = "INSERT INTO {$this->table}({$insert_fields})";

        // VALUES
        $field_placeholders = implode(",", $field_placeholders);
        $sql[] = "VALUES({$field_placeholders})";

        return new SQL(implode(" ", $sql), $params);
    }
}