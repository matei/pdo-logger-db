<?php

namespace Matei\PdoLoggerDb\DB\Adapter\Pdo;

use Magento\ResourceConnections\DB\Adapter\Pdo\MysqlProxy as MagentoMysqlProxy;

class MysqlProxy extends MagentoMysqlProxy
{
    const CONN_SLAVE = 'slave';
    const CONN_MASTER = 'master';

    /**
     * Select defined connection
     *
     * @param string|\Magento\Framework\DB\Select $sql The SQL statement with placeholders.
     *
     * @return \Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    protected function selectConnection($sql = null)
    {
        if (!$this->masterConnectionOnly &&
            ($sql === null || $this->isReadOnlyQuery($sql))
        ) {
            $this->logger->setConnectionName(self::CONN_SLAVE);
            return $this->getSlaveConnection();
        }
        $this->logger->setConnectionName(self::CONN_MASTER);
        $this->setUseMasterConnection();
        return $this->getMasterConnection();
    }

}
