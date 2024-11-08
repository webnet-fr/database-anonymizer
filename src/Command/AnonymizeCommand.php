<?php

namespace WebnetFr\DatabaseAnonymizer\Command;

use Doctrine\DBAL\DBALException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WebnetFr\DatabaseAnonymizer\Anonymizer;
use WebnetFr\DatabaseAnonymizer\Config\TargetFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\GeneratorFactoryInterface;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class AnonymizeCommand extends Command
{
    use AnonymizeCommandTrait;

    /**
     * @var GeneratorFactoryInterface
     */
    private $generatorFactory;

    /**
     * @param GeneratorFactoryInterface $generatorFactory
     */
    public function __construct(GeneratorFactoryInterface $generatorFactory)
    {
        parent::__construct();

        $this->generatorFactory = $generatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('webnet-fr:anonymizer:anonymize')
            ->setDescription('Anoymize database.')
            ->setHelp('Anoymize database according to GDPR (General Data Protection Regulation).')
            ->addArgument('config', InputArgument::REQUIRED, 'Configuration file.')
            ->addOption('url', 'U', InputOption::VALUE_REQUIRED, 'Database connection string.')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Database type.')
            ->addOption('host', 'H', InputOption::VALUE_REQUIRED, 'Database host.')
            ->addOption('port', 'P', InputOption::VALUE_REQUIRED, 'Database port.')
            ->addOption('database', 'd', InputOption::VALUE_REQUIRED, 'Database name.')
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'User.')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Password.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $questionHelper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to anonymize your database?', false);

        if (!$input->getOption('no-interaction') && !$questionHelper->ask($input, $output, $question)) {
            return 1;
        }

        try {
            $connection = $this->getConnectionFromInput($input);
        } catch (DBALException $e) {
            $connection = null;
        }

        if (!$connection) {
            $output->writeln(sprintf('<error>Unable to establish a connection.</error>'));

            return 1;
        }

        $configFile = $input->getArgument('config');
        $configFilePath = realpath($input->getArgument('config'));
        if (!is_file($configFilePath)) {
            $output->writeln(sprintf('<error>Configuration file "%s" does not exist.</error>', $configFile));

            return 1;
        }

        $config = $this->getConfigFromFile($configFilePath);

        $targetFactory = new TargetFactory($this->generatorFactory);
        $targetFactory->setConnection($connection);
        $targetTables = $targetFactory->createTargets($config);

        $anonymizer = new Anonymizer();
        $anonymizer->anonymize($connection, $targetTables);

        return 0;
    }
}
