<?php
declare(strict_types=1);
namespace Stk2k\PowerPDO\context;

use Psr\Log\LoggerInterface;

use Stk2k\PowerPDO\PowerPDO;

abstract class BaseContext
{
    private $pdo;
    private $logger;

    /**
     * Constructor
     *
     * @param PowerPDO $pdo
     * @param  LoggerInterface $logger
     */
    public function __construct(PowerPDO $pdo, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    /**
     * Returns power PDO object
     */
    protected function getPowerPDO() : PowerPDO
    {
        return $this->pdo;
    }

    /**
     * Returns Logger object
     */
    protected function getLogger() : LoggerInterface
    {
        return $this->logger;
    }
}