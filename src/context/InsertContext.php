<?php /** @noinspection DuplicatedCode */
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Stk2k\PowerPDO\util\ArrayUtil;

class InsertContext extends BaseContext
{
    private $table;
    private $fields;
    private $values;
    private $placeholders;

    /**
     * specifies table name and fields
     */
    public function into(string $table, string $fields = "") : self
    {
        $this->table = $table;
        $this->fields = $fields;
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
     */
    public function execute() : void
    {
        $pdo = $this->getPDO();
        $logger = $this->getLogger();

        // generate SQL
        $sql = $this->buildInsertSQL();

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
     * build INSERT sql
     */
    private function buildInsertSQL() : string
    {
        // INSERT INTO
        $flds = explode(",", $this->fields);
        $sql[] = "INSERT INTO {$this->table}({$this->fields})";

        // VALUES
        if (is_string($this->values)){
            $sql[] = "VALUES({$this->values})";
        }
        elseif (is_object($this->values)){
            $entity_fields_map = get_object_vars($this->values);
            $values = [];
            foreach($flds as $field){
                $values[] = $entity_fields_map[$field] ?? null;
            }
            $sql[] = "VALUES({$values})";
        }

        return  implode(" ", $sql);
    }
}