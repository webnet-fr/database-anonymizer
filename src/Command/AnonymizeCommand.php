<?php

namespace WebnetFr\DatabaseAnonymizer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument('db_url', InputArgument::REQUIRED, 'Database connection string.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to anonymize your database?', false);

        if (!$questionHelper->ask($input, $output, $question)) {
            return;
        }

        $configFilePath = realpath($input->getArgument('config'));
        $config = $this->getConfigFromFile($configFilePath);

        $generatorFactory = new ChainGeneratorFactory();
        $generatorFactory->addFactory(new ConstantGeneratorFactory())
            ->addFactory(new FakerGeneratorFactory());

        $targetFactory = new TargetFactory($generatorFactory);
        $targetTables = $targetFactory->createTargets($config);

        $connection = $this->getConnection($input->getArgument('db_url'));

        $anonymizer = new Anonymizer();
        $anonymizer->anonymize($connection, $targetTables);
    }
}
