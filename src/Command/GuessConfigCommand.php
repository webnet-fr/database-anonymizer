<?php

namespace WebnetFr\DatabaseAnonymizer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigGuesser;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigWriter;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class GuessConfigCommand extends Command
{
    use AnonymizeCommandTrait;

    /**
     * @var ConfigGuesser
     */
    private $configGuesser;

    /**
     * @var ConfigWriter
     */
    private $configWriter;

    /**
     * @param ConfigGuesser $configGuesser
     * @param ConfigWriter  $configWriter
     */
    public function __construct(ConfigGuesser $configGuesser, ConfigWriter $configWriter)
    {
        parent::__construct();

        $this->configGuesser = $configGuesser;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('webnet-fr:anonymizer:guess-config')
            ->setDescription('Guess anonymizer configuration.')
            ->setHelp('Guess and dump anonymizer configuration.')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Dump to specified file.')
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
        $connection = $this->getConnectionFromInput($input);

        $hints = $this->configGuesser::guess($connection);
        $config = $this->configWriter->write($hints);

        $file = $input->getOption('file');
        if ($file) {
            \file_put_contents($file, $config);
        } else {
            $output->write($config);
        }

        return 0;
    }
}
