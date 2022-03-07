<?php
declare(strict_types=1);
namespace Stk2k\PowerPdo;

use PDO;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use Stk2k\PowerPdo\context\InsertContext;
use Stk2k\PowerPdo\context\SelectContext;
use Stk2k\PowerPdo\context\UpdateContext;

class PowerPDO
{
    private $pdo;
    private $logger;

    /**
     * constructor
     *
     * @param PDO $pdo
     * @param ?LoggerInterface $logger
     * @param ?array $pdo_options
     */
    public function __construct(
        PDO $pdo,
        LoggerInterface $logger = null,
        array $pdo_options = null)
    {
        $this->pdo = $pdo;
        $this->logger = $logger ?? new NullLogger;

        if (is_array($pdo_options)){
            foreach($pdo_options as $k => $v){
                $pdo->setAttribute($k, $v);
            }
        }
    }

    /**
     * PDO
     */
    public function getPDO() : PDO
    {
        return $this->pdo;
    }

    /**
     * Logger
     */
    public function getLogger() : LoggerInterface
    {
        return $this->logger;
    }

    /**
     * SELECT
     */
    public function select(string $fields = null) : SelectContext
    {
        if ($fields == null){
            return  new SelectContext($this->pdo, $this->logger);
        }
        return (new SelectContext($this->pdo, $this->logger))->fields($fields);
    }

    /**
     * INSERT
     */
    public function insert() : InsertContext
    {
        return  new InsertContext($this->pdo, $this->logger);
    }

    /**
     * UPDATE
     */
    public function update(string $table = null) : UpdateContext
    {
        if ($table == null){
            return  new UpdateContext($this->pdo, $this->logger);
        }
        return (new UpdateContext($this->pdo, $this->logger))->table($table);
    }

    /**
     * DELETE
     */
    public function delete() : UpdateContext
    {
        return  new UpdateContext($this->pdo, $this->logger);
    }
}