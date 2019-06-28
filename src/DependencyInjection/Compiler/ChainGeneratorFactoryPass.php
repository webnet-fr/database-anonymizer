<?php

namespace WebnetFr\DatabaseAnonymizer\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ChainGeneratorFactory;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ChainGeneratorFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ChainGeneratorFactory::class)) {
            return;
        }

        $chainGeneratorFactoryDefinition = $container->findDefinition(ChainGeneratorFactory::class);
        $generatorFactories = $container->findTaggedServiceIds('database_anonymizer.generator_factory');

        foreach ($generatorFactories as $id => $tags) {
            $factoryClass = $container->getDefinition($id)->getClass();

            if (ChainGeneratorFactory::class !== !$factoryClass && !\is_subclass_of($factoryClass, ChainGeneratorFactory::class)) {
                $chainGeneratorFactoryDefinition->addMethodCall('addFactory', [new Reference($id)]);
            }
        }
    }
}
