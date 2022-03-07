<?php
declare(strict_types=1);

namespace Stk2k\PowerPDO\util;

class ArrayUtil
{
    /**
     * merge two values
     *
     * @param ?array $p1
     * @param ?array $p2
     *
     * @return array
     */
    public static function merge(?array $p1, ?array $p2) : array
    {
        return array_merge($p1 ?? [], $p2 ?? []);
    }
}