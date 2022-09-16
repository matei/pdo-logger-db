<?php
namespace Matei\PdoLoggerDb\Console\Command;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueryLogStartNewSession extends Command
{
    const COMMAND_NAME = 'dev:db-query-log:new-session';

    const SUCCESS_MESSAGE = "DB query logging disabled.";

    /**
     * input parameter name
     */
    const INPUT_ARG_NAME = 'name';

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
            ->setDescription('Start New Session')
            ->setDefinition(
                [
                    new InputOption(
                        self::INPUT_ARG_NAME,
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Name of the session',
                        "default"
                    ),
                ]
            );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $createdAt = date('Y-m-d H:i:s');
        $name = $input->getOption(static::INPUT_ARG_NAME);
        if ($name == 'default') {
            $name = 'Session created at ' . $createdAt;
        }
        $this->adapter->insert(
            $this->adapter->getTableName('pdo_log_session'),
            [
                'name' => $name,
                'created_at' => $createdAt
            ]
        );
    }
}
