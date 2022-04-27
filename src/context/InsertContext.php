<?php
/** @noinspection DuplicatedCode */
/** @noinspection SqlNoDataSourceInspection */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Stk2k\PowerPDO\exception\PowerPdoException;
use Stk2k\PowerPDO\util\ArrayUtil;

class InsertContext extends BaseContext
{
    private $table;
    private $values;
    private $placeholders;

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
     * @param array $placeholders
     *
     * @return $this
     */
    public function values($values, array $placeholders = []) : self
    {
        $this->values = $values;
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
     * @throws PowerPdoException
     */
    public function execute() : void
    {
        // generate SQL
        $sql = $this->buildInsertSQL();

        // execute SQL
        $this->getPowerPDO()->execute($sql, $this->placeholders);
    }

    /**
     * build INSERT sql
     *
     * @throws
     */
    private function buildInsertSQL() : string
    {
        // fields from values key
        $field_placeholders = [];
        $values_map = null;
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
        foreach($values_map as $key => $value)
        {
            $field_placeholders[] = ":{$key}";
            $this->placeholders[":{$key}"] = $value;
        }

        // INSERT INTO(fields, ...)
        $insert_fields = implode(",", array_keys($values_map));
        $sql[] = "INSERT INTO {$this->table}({$insert_fields})";

        // VALUES
        $field_placeholders = implode(",", $field_placeholders);
        $sql[] = "VALUES({$field_placeholders})";

        return  implode(" ", $sql);
    }
}