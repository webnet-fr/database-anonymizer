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
     * @param array $params
     *
     * @return Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getConnectionFromInput(InputInterface $input)
    {
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

        return $this->getConnection($params);
    }

    /**
     * @param array $params
     *
     * @return Connection
     *
     * @throws \Doctrine\DBAL\DBALException
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
