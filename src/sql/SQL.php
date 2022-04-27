<?php
declare(strict_types=1);

namespace Stk2k\PowerPDO\sql;

class SQL
{
    private $text;
    private $params;

    public function __construct(string $text, array $params = null)
    {
        $this->text = $text;
        $this->params = $params ?? [];
    }

    /**
     * Returns SQL command Text
     *
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * Returns SQL parameters
     *
     * @return array
     */
    public function getParams() : array
    {
        return $this->params;
    }

    /**
     * Converts to string
     *
     * @return string
     */
    public function __toString() : string
    {
        return "{$this->text} [params]" . json_encode($this->params);
    }
}