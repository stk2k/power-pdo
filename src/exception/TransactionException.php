<?php
declare(strict_types=1);
namespace Stk2k\PowerPdo\exception;

use Exception;

class TransactionException extends PowerPdoException
{
    /**
     * constructor
     *
     * @param string $message
     * @param ?Exception $cause
     */
    public function __construct(string $message, Exception $cause = null)
    {
        parent::__construct($message, $cause);
    }
}