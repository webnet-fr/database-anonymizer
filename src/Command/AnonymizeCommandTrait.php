<?php

namespace WebnetFr\DatabaseAnonymizer\Command;

use Doctrine\DBAL\Configuration as DoctrineDBALConfiguration;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use WebnetFr\DatabaseAnonymizer\Config\Configuration;
use WebnetFr\DatabaseAnonymizer\Config\YamlAnonymizerLoader;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
trait AnonymizeCommandTrait
{
    /**
     * @param string $dbUrl
     *
     * @return \Doctrine\DBAL\Connection
     */
    protected function getConnection($dbUrl)
    {
        $params = ['url' => $dbUrl];
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
