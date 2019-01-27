<?php

namespace WebnetFr\DatabaseAnonymizer\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Configuration implements ConfigurationInterface
{
    use ConfigurationTrait;

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('webnet_fr_database_anonymizer');

        $this->configureAnonymizer($treeBuilder->getRootNode());

        return $treeBuilder;
    }
}
