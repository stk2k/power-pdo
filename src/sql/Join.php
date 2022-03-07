<?php
declare(strict_types=1);
namespace Stk2k\PowerPdo\sql;

class Join
{
    public const JOIN_TYPE_INNER = "INNER";
    public const JOIN_TYPE_OUTER = "OUTER";
    public const JOIN_TYPE_LEFT = "LEFT";
    public const JOIN_TYPE_RIGHT = "RIGHT";

    public $join_type;
    public $table;
    public $on;

    /**
     * コンストラクタ
     */
    public function __construct(string $join_type, string $table, string $on)
    {
        $this->join_type = $join_type;
        $this->table = $table;
        $this->on = $on;
    }

    /**
     * joinタイプ
     */
    public function getJoinType() : string
    {
        return  $this->join_type;
    }

    /**
     * テーブル名
     */
    public function getTable() : string
    {
        return $this->table;
    }

    /**
     * 結合条件
     */
    public function getOn() : string
    {
        return $this->on;
    }

    /**
     * SQL
     */
    public function toSQL() : string
    {
        return  "{$this->join_type} JOIN {$this->table} ON {$this->on}";
    }
}