<?php /** @noinspection DuplicatedCode */
declare(strict_types=1);
namespace Stk2k\PowerPdo\context;

use PDO;
use PDOStatement;

use Stk2k\PowerPdo\sql\Join;
use Stk2k\PowerPdo\util\ArrayUtil;

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
        $stmt = $this->executeSQL($entity_class);

        return $stmt->fetchAll();
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
        $stmt = $this->executeSQL($entity_class);

        return $stmt->fetch();
    }

    /**
     * execute SQL
     *
     * @param string $entity_class
     *
     * @return PDOStatement
     */
    private function executeSQL(string $entity_class) : PDOStatement
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
        $stmt->setFetchMode(PDO::FETCH_CLASS, $entity_class);

        // execute SQL
        $stmt->execute();

        return $stmt;
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