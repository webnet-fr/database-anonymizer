<?php

namespace WebnetFr\DatabaseAnonymizer\Command;

use Doctrine\DBAL\Configuration as DoctrineDBALConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Console\Input\InputInterface;
use WebnetFr\DatabaseAnonymizer\Config\Configuration;
use WebnetFr\DatabaseAnonymizer\Config\YamlAnonymizerLoader;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
trait AnonymizeCommandTrait
{
    /**
     * @param InputInterface $input
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return Connection|null
     */
    protected function getConnectionFromInput(InputInterface $input)
    {
        if ($dbURL = $input->getOption('url')) {
            return $this->getConnection(['url' => $dbURL]);
        } elseif (
            ($type = $input->getOption('type'))
            && ($host = $input->getOption('host'))
            && ($port = $input->getOption('port'))
            && ($database = $input->getOption('database'))
            && ($user = $input->getOption('user'))
        ) {
            return $this->getConnection([
                'driver' => $type,
                'host' => $host,
                'port' => $port,
                'dbname' => $database,
                'user' => $user,
                'password' => $input->getOption('password'),
            ]);
        }

        return null;
    }

    /**
     * @param array $params
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return Connection
     */
    protected function getConnection(array $params): Connection
    {
        $config = new DoctrineDBALConfiguration();

        return DriverManager::getConnection($params, $config);
    }

    /**
     * @param string $configFilePath
     *
     * @return array
     */
    protected function getConfigFromFile(string $configFilePath)
    {
        $fileLocator = new FileLocator();
        $loaderResolver = new LoaderResolver([new YamlAnonymizerLoader($fileLocator)]);
        $delegatingLoader = new DelegatingLoader($loaderResolver);
        $rawConfig = $delegatingLoader->load($configFilePath);

        $configuration = new Configuration();
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $rawConfig);
    }
}
