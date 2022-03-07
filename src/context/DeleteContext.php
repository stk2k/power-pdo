<?php /** @noinspection DuplicatedCode */
declare(strict_types=1);
namespace Stk2k\PowerPdo\context;

use Stk2k\PowerPdo\util\ArrayUtil;

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
        $pdo = $this->getPDO();
        $logger = $this->getLogger();

        // generate SQL
        $sql = $this->buildDeleteSQL();

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