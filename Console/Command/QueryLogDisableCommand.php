<?php
namespace Matei\PdoLoggerDb\Console\Command;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Logger\LoggerProxy;
use Magento\Framework\Exception\FileSystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class QueryLogDisableCommand extends Command
{
    const COMMAND_NAME = 'dev:db-query-log:disable';

    const SUCCESS_MESSAGE = "DB query logging disabled.";

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
            ->setDescription('Disable DB Query Logging');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     * @throws FileSystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = [LoggerProxy::PARAM_ALIAS => LoggerProxy::LOGGER_ALIAS_DISABLED];
        $this->deployConfigWriter->saveConfig([ConfigFilePool::APP_ENV => [LoggerProxy::CONF_GROUP_NAME => $data]]);

        $output->writeln("<info>". self::SUCCESS_MESSAGE . "</info>");
    }
}
