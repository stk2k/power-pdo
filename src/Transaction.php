<?php
declare(strict_types=1);
namespace Stk2k\PowerPdo;

use PDO;

use Stk2k\PowerPdo\exception\TransactionException;

class Transaction
{
    /** @var PDO */
    private $source;

    /**
     * constructor
     *
     * @param PDO|PowerPDO $source
     */
    public function __construct($source)
    {
        if ($source instanceof PowerPDO){
            $source = $source->getPDO();
        }
        $this->source = $source;
    }

    /**
     * Begin transaction
     *
     * @throws TransactionException
     */
    public function begin() : void
    {
        if (!$this->source->beginTransaction()){
            throw new TransactionException("Failed to begin transaction.");
        }
    }

    /**
     * Commit transaction
     *
     * @throws TransactionException
     */
    public function commit() : void
    {
        if (!$this->source->commit()){
            throw new TransactionException("Failed to commit transaction.");
        }
    }

    /**
     * Rollback transaction
     *
     * @throws TransactionException
     */
    public function rollback() : void
    {
        if (!$this->source->rollBack()){
            throw new TransactionException("Failed to rollback transaction.");
        }
    }
}