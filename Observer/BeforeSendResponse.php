<?php
namespace Matei\PdoLoggerDb\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Matei\PdoLoggerDb\DB\Logger\Db;

class BeforeSendResponse implements ObserverInterface
{
    /**
     * @var Db
     */
    private $dbLogger;

    public function __construct(Db $dbLogger)
    {
        $this->dbLogger = $dbLogger;
    }


    /**
     * Perform inserts at the end of the request
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->dbLogger->performInserts();
    }
}
