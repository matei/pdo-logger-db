<?php
namespace Matei\PdoLoggerDb\Console\Command;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueryLogTruncateCommand extends Command
{
    const COMMAND_NAME = 'dev:db-query-log:truncate';

    const SUCCESS_MESSAGE = "Logs were truncated";

    /**
     * @var AdapterInterface
     */
    private $adapter;


    public function __construct(
        ResourceConnection $resourceConnection,
                           $name = null
    ) {
        parent::__construct($name);
        $this->adapter = $resourceConnection->getConnection('core_write');
    }

    public function configure()
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription('Truncate DB Query Logs and Sessions');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->adapter->truncateTable($this->adapter->getTableName('pdo_log_line'));
        $this->adapter->delete($this->adapter->getTableName('pdo_log_session'));
        $output->writeln("<info>". self::SUCCESS_MESSAGE . "</info>");
    }
}
