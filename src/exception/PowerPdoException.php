<?php
declare(strict_types=1);
namespace Stk2k\PowerPDO\exception;

use Exception;

class PowerPdoException extends Exception
{
    /**
     * constructor
     *
     * @param string $message
     * @param ?Exception $cause
     */
    public function __construct(string $message, Exception $cause = null)
    {
        parent::__construct($message, 0, $cause);
    }
}