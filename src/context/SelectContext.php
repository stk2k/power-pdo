<?php
/** @noinspection DuplicatedCode */
/** @noinspection SqlNoDataSourceInspection */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Psr\Log\LoggerInterface;

use Stk2k\PowerPDO\PowerPDO;
use Stk2k\PowerPDO\sql\Join;
use Stk2k\PowerPDO\util\ArrayUtil;
use Stk2k\PowerPDO\sql\SQL;

class SelectContext extends BaseContext
{
    private $fields;
    private $table;
    private $table_alias;
    private $values;
    private $where;     /* array */

    /** @var Join[] */
    private $joins;

    /**
     * Constructor
     *
     * @param PowerPDO $pdo
     * @param  LoggerInterface $logger
     */
    public function __construct(PowerPDO $pdo, LoggerInterface $logger)
    {
        parent::__construct($pdo, $logger);

        $this->fields = '*';
    }

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
        $this->values = ArrayUtil::merge($this->values, $values);
        return $this;
    }

    /**
     * get all records
     *
     * @param string|null $entity_class
     *
     * @return array
     */
    public function getAll(string $entity_class = null) : array
    {
        // generate SQL
        $sql = $this->buildSelectSQL();

        if ($entity_class){
            return $this->getPowerPDO()->fetchAllObjects($entity_class, $sql);
        }
        return $this->getPowerPDO()->fetchAllObjects($entity_class, $sql);
    }

    /**
     * get first record
     *
     * @param string|null $entity_class
     *
     * @return mixed
     */
    public function getFirst(string $entity_class = null)
    {
        // generate SQL
        $sql = $this->buildSelectSQL();

        if ($entity_class){
            return $this->getPowerPDO()->fetchObject($entity_class, $sql);
        }

        return $this->getPowerPDO()->fetchAssoc($sql);
    }

    /**
     * build SELECT sql
     */
    private function buildSelectSQL() : SQL
    {
        $params = [];

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

        // Placeholders
        if (is_array($this->values)){
            foreach($this->values as $key => $value){
                $params[":{$key}"] = $value;
            }
        }

        return new SQL(implode(" ", $sql), $params);
    }
}