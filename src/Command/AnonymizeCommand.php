<?php

namespace WebnetFr\DatabaseAnonymizer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WebnetFr\DatabaseAnonymizer\Anonymizer;
use WebnetFr\DatabaseAnonymizer\Config\TargetFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ChainGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ConstantGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\DatetimeGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\FakerGeneratorFactory;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class AnonymizeCommand extends Command
{
    use AnonymizeCommandTrait;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('webnet-fr:anonymizer:anonymize')
            ->setDescription('Anoymize database.')
            ->setHelp('Anoymize database according to GDPR (General Data Protection Regulation).')
            ->addArgument('config', InputArgument::REQUIRED, 'Configuration file.')
            ->addOption('url', 'U', InputOption::VALUE_REQUIRED, 'Database connection string.')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Database type.')
            ->addOption('host', 'H', InputOption::VALUE_REQUIRED, 'Database connection string.')
            ->addOption('port', 'P', InputOption::VALUE_REQUIRED, 'Database connection string.')
            ->addOption('database', 'd', InputOption::VALUE_REQUIRED, 'Database connection string.')
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'Database connection string.')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Database connection string.')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to anonymize your database?', false);

        if (!$input->getOption('no-interaction') && !$questionHelper->ask($input, $output, $question)) {
            return;
        }

        $configFile = $input->getArgument('config');
        $configFilePath = realpath($input->getArgument('config'));
        if (!is_file($configFilePath)) {
            $output->writeln(sprintf('<error>Configuration file "%s" does not exist.</error>', $configFile));

            return;
        }

        $config = $this->getConfigFromFile($configFilePath);

        $generatorFactory = new ChainGeneratorFactory();
        $generatorFactory->addFactory(new ConstantGeneratorFactory())
            ->addFactory(new FakerGeneratorFactory());

        $targetFactory = new TargetFactory($generatorFactory);
        $targetTables = $targetFactory->createTargets($config);

        if ($dbURL = $input->getOption('url')) {
            $params = ['url' => $dbURL];
        } else {
            $params = [
                'driver' => $input->getOption('type'),
                'host' => $input->getOption('host'),
                'port' => $input->getOption('port'),
                'dbname' => $input->getOption('database'),
                'user' => $input->getOption('user'),
                'password' => $input->getOption('password'),
            ];
        }

        $connection = $this->getConnection($params);

        $anonymizer = new Anonymizer();
        $anonymizer->anonymize($connection, $targetTables);
    }
}
