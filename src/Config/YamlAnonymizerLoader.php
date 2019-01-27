<?php

namespace WebnetFr\DatabaseAnonymizer\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class YamlAnonymizerLoader extends FileLoader
{
    /**
     * @inheritdoc
     */
    public function load($resource, $type = null)
    {
        $configValues = Yaml::parse(file_get_contents($resource));

        return $configValues;
    }

    /**
     * @inheritdoc
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yaml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
