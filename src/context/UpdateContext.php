<?php
/** @noinspection DuplicatedCode */
/** @noinspection SqlNoDataSourceInspection */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Psr\Log\LoggerInterface;
use Stk2k\PowerPDO\PowerPDO;
use Stk2k\PowerPDO\util\ArrayUtil;
use Stk2k\PowerPDO\sql\SQL;

class UpdateContext extends BaseContext
{
    private $table;
    private $values;
    private $where;     /* array */

    /**
     * @param PowerPDO $db
     * @param LoggerInterface $logger
     * @param string $table
     */
    public function __construct(PowerPDO $db, LoggerInterface $logger, string $table)
    {
        parent::__construct($db, $logger);

        $this->table = $table;
    }

    /**
     * specifies table name
     */
    public function table(string $table) : self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * specifies table values
     */
    public function set(string $field, $value) : self
    {
        $this->values[$field] = $value;
        return $this;
    }

    /**
     * specifies table values
     *
     * @param object|array $values
     *
     * @return $this
     */
    public function values($values) : self
    {
        $this->values = is_array($values) ? $values : (is_object($values) ? get_object_vars($values) : []);
        return $this;
    }

    /**
     * WHERE caluse
     */
    public function where(string $where_clause, array $placeholders = []) : self
    {
        $this->where[] = $where_clause;
        if (!empty($placeholders)){
            $this->values = ArrayUtil::merge($this->values, $placeholders);
        }
        return $this;
    }

    /**
     * execute SQL
     */
    public function execute() : void
    {
        // generate SQL
        $sql = $this->buildUpdateSQL();

        $this->getPowerPDO()->execute($sql);
    }

    /**
     * build UPDATE sql
     */
    private function buildUpdateSQL() : SQL
    {
        $params = [];

        // UPDATE
        $sql[] = "UPDATE {$this->table}";

        // SET
        $sql[] = "SET";
        $set = [];
        foreach($this->values as $k => $v)
        {
            $set[] = "{$k}=:{$k}";
            $params[":{$k}"] = $v;
        }
        $sql[] = implode(",", $set);

        // WHERE
        if ($this->where)
        {
            $sql[] = "WHERE";
            foreach($this->where as $where){
                $sql[] = $where;
            }
        }

        return new SQL(implode(" ", $sql), $params);
    }
}