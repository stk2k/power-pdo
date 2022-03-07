<?php
declare(strict_types=1);
namespace Stk2k\PowerPdo\context;

use PDO;
use Psr\Log\LoggerInterface;

abstract class BaseContext
{
    private $pdo;
    private $logger;

    /**
     * コンストラクタ
     *
     * @param PDO $pdo
     * @param  LoggerInterface $logger
     */
    public function __construct(PDO $pdo, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    /**
     * PDOオブジェクト
     */
    protected function getPDO() : PDO
    {
        return $this->pdo;
    }

    /**
     * Loggerオブジェクト
     */
    protected function getLogger() : LoggerInterface
    {
        return $this->logger;
    }
}