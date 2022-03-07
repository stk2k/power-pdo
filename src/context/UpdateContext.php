<?php /** @noinspection DuplicatedCode */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Stk2k\PowerPDO\util\ArrayUtil;

class UpdateContext extends BaseContext
{
    private $table;
    private $sets;
    private $values;
    private $where;     /* array */
    private $placeholders;

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
    public function set(string $field, $value, array $placeholders = []) : self
    {
        $this->sets[$field] = $value;
        $this->values = null;
        if (!empty($placeholders)){
            $this->placeholders = ArrayUtil::merge($this->placeholders, $placeholders);
        }
        return $this;
    }

    /**
     * specifies table values
     *
     * @param object $values
     * @param array $placeholders
     *
     * @return $this
     */
    public function values(object $values, array $placeholders = []) : self
    {
        $this->values = get_object_vars($values);
        $this->sets = null;
        if (!empty($placeholders)){
            $this->placeholders = ArrayUtil::merge($this->placeholders, $placeholders);
        }
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
        $pdo = $this->getPDO();
        $logger = $this->getLogger();

        // generate SQL
        $sql = $this->buildUpdateSQL();

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

        // execute SQL
        $stmt->execute();
    }

    /**
     * build UPDATE sql
     */
    private function buildUpdateSQL() : string
    {
        // UPDATE
        $sql[] = "UPDATE {$this->table}";

        // SET
        $sql[] = "SET";
        $set = [];
        foreach(ArrayUtil::merge($this->sets, $this->values) as $k => $v)
        {
            $set[] = "{$k}={$v}";
        }
        $sql[] = implode(",", $set);

        // WHERE
        $sql[] = "WHERE";
        foreach($this->where as $where){
            $sql[] = $where;
        }

        return  implode(" ", $sql);
    }
}