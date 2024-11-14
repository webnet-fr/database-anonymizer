<?php

namespace WebnetFr\DatabaseAnonymizer\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\GeneratorFactoryInterface;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class WebnetFrDatabaseAnonymizerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(GeneratorFactoryInterface::class)
            ->addTag('database_anonymizer.generator_factory')
        ;

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }
}
