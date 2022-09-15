<?php
namespace Matei\PdoLoggerDb\DB\Logger;

use Magento\Framework\DB\Logger\LoggerProxy as MagentoLoggerProxy;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\DB\Logger\FileFactory;
use Magento\Framework\DB\Logger\QuietFactory;

class LoggerProxy extends MagentoLoggerProxy
{
    /**
     * Configuration group name
     */
    const CONF_GROUP_NAME = 'db_logger';

    /**
     * Logger alias param name
     */
    const PARAM_ALIAS = 'output';

    /**
     * Logger log all param name
     */
    const PARAM_LOG_ALL = 'log_everything';

    /**
     * Logger query time param name
     */
    const PARAM_QUERY_TIME = 'query_time_threshold';

    /**
     * Logger call stack param name
     */
    const PARAM_CALL_STACK = 'include_stacktrace';

    /**
     * File logger alias
     */
    const LOGGER_ALIAS_FILE = 'file';

    /**
     * DB logger alias
     */
    const LOGGER_ALIAS_DB = 'db';

    /**
     * Quiet logger alias
     */
    const LOGGER_ALIAS_DISABLED = 'disabled';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var DbFactory
     */
    private $dbFactory;

    /**
     * @var QuietFactory
     */
    private $quietFactory;

    /**
     * @var bool
     */
    private $loggerAlias;

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
     * LoggerProxy constructor.
     * @param FileFactory $fileFactory
     * @param QuietFactory $quietFactory
     * @param bool $loggerAlias
     * @param bool $logAllQueries
     * @param float $logQueryTime
     * @param bool $logCallStack
     */
    public function __construct(
        FileFactory $fileFactory,
        QuietFactory $quietFactory,
        DBFactory $DBFactory,
        $loggerAlias,
        $logAllQueries = true,
        $logQueryTime = 0.001,
        $logCallStack = true
    ) {
        $this->fileFactory = $fileFactory;
        $this->quietFactory = $quietFactory;
        $this->loggerAlias = $loggerAlias;
        $this->logAllQueries = $logAllQueries;
        $this->logQueryTime = $logQueryTime;
        $this->logCallStack = $logCallStack;
    }

    /**
     * Get logger object. Initialize if needed.
     * @return LoggerInterface
     */
    private function getLogger()
    {
        if ($this->logger === null) {
            switch ($this->loggerAlias) {
                case self::LOGGER_ALIAS_FILE:
                    $this->logger = $this->fileFactory->create(
                        [
                            'logAllQueries' => $this->logAllQueries,
                            'logQueryTime' => $this->logQueryTime,
                            'logCallStack' => $this->logCallStack,
                        ]
                    );
                    break;

                case self::LOGGER_ALIAS_DB:
                    $this->logger = $this->dbFactory->create(
                        [
                            'logAllQueries' => $this->logAllQueries,
                            'logQueryTime' => $this->logQueryTime,
                            'logCallStack' => $this->logCallStack
                        ]
                    );

                default:
                    $this->logger = $this->quietFactory->create();
                    break;
            }
        }
        return $this->logger;
    }
}
