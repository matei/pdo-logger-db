<?php
namespace Matei\PdoLoggerDb\DB\Logger;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Logger\LoggerAbstract;

class Db extends LoggerAbstract
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var int
     */
    private $timer;

    /**
     * @var bool
     */
    private $logAllQueries;

    /**
     * @var float
     */
    private $logQueryTime;

    /**
     * @var bool
     */
    private $logCallStack;

    /**
     * @var bool
     */
    public static $insideLogging = false;

    /**
     * @param ResourceConnection $resource
     * @param $logAllQueries
     * @param $logQueryTime
     * @param $logCallStack
     */
    public function __construct(
        ResourceConnection $resource,
                   $logAllQueries = false,
                   $logQueryTime = 0.05,
                   $logCallStack = false
    ) {
        parent::__construct($logAllQueries, $logQueryTime, $logCallStack);
        $this->resource = $resource;
    }

    public function log($str)
    {
        //do nothing. this method is called just in LoggerProxy and logStats in File logger
    }

    /**
     * This must always be the entrypoint, otherwise there will be infinite recursion
     *
     * @param $type
     * @param $sql
     * @param $bind
     * @param $result
     * @return void
     * @throws \Zend_Db_Statement_Exception
     */
    public function logStats($type, $sql, $bind = [], $result = null)
    {
        if (self::$insideLogging) {
            return;
        }
        self::$insideLogging = true;

        switch ($type) {
            case self::TYPE_CONNECT:
                $sql = 'CONNECT';
                break;
            case self::TYPE_TRANSACTION:
                $sql = 'TRANSACTION ' . $sql;
                break;
        }

        $insert = [
            'session_id' => $this->getCurrentSessionId(),
            'content' => $sql,
            'time' => sprintf('%.4f', microtime(true) - $this->timer),
            'stacktrace' => Debug::backtrace(true, false),
            'row_count' => $result instanceof \Zend_Db_Statement_Pdo ? $result->rowCount() : 0
        ];

        $this->getConnection()->insert(
            $this->getConnection()->getTableName('pdo_log_line'),
            $insert
        );

        self::$insideLogging = false;
    }


    private function getCurrentSessionId()
    {
        $select = $this->getConnection()->select()->from(
            $this->getConnection()->getTableName('pdo_log_session')
        )->order('entity_id desc')
        ->limit(1);
        $session =  $select->query()->fetch(\PDO::FETCH_ASSOC);
        if ($session && isset($session['session_id'])) {
            return $session['session_id'];
        } else {
            $createdAt = date('Y-m-d H:i:s');
            $name = 'Test ' . $createdAt;
            $this->getConnection()->insert(
                $this->getConnection()->getTableName('pdo_log_session'),
                [
                    'name' => $name,
                    'created_at' => $createdAt
                ]
            );
            return $this->getConnection()->lastInsertId();
        }
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        if (is_null($this->connection)) {
            $this->connection = $this->resource->getConnection('core_write');
        }

        return $this->connection;
    }

    public function critical(\Exception $e)
    {
        // TODO: Implement critical() method.
    }
}
