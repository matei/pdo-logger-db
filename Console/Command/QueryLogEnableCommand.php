<?php
namespace Matei\PdoLoggerDb\Console\Command;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Matei\PdoLoggerDb\DB\Logger\LoggerProxy;
use Magento\Framework\Exception\FileSystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class QueryLogEnableCommand extends Command
{
    const COMMAND_NAME = 'dev:db-query-log:enable';

    const SUCCESS_MESSAGE = "DB query logging enabled.";

    /**
     * @var Writer
     */
    private $deployConfigWriter;


    public function __construct(
        Writer $deployConfigWriter,
               $name = null
    ) {
        parent::__construct($name);
        $this->deployConfigWriter = $deployConfigWriter;
    }

    public function configure()
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription('Enable DB Query Logging');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     * @throws FileSystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = [LoggerProxy::PARAM_ALIAS => LoggerProxy::LOGGER_ALIAS_DB];

        $logAllQueries = true;
        $logQueryTime = '0.001';
        $logCallStack = true;

        $data[LoggerProxy::PARAM_LOG_ALL] = 1;
        $data[LoggerProxy::PARAM_QUERY_TIME] = number_format($logQueryTime, 3);
        $data[LoggerProxy::PARAM_CALL_STACK] = 1;

        $configGroup[LoggerProxy::CONF_GROUP_NAME] = $data;

        $this->deployConfigWriter->saveConfig([ConfigFilePool::APP_ENV => $configGroup]);

        $output->writeln("<info>". self::SUCCESS_MESSAGE . "</info>");
    }
}
