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
     * {@inheritdoc}
     */
    public function load(mixed $resource, ?string $type = null): mixed
    {
        return Yaml::parse(file_get_contents($resource));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $resource, ?string $type = null): bool
    {
        return \is_string($resource) && 'yaml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
